<?php
/*
   * App Controller Class
   * Resolves URL and Call the Required service
   * URL FORMAT - generic CRUD services {
   *              GET    - /model    - return all records - user 
   *                     - /model/1  - return model with record id 1
   *              PUT    - /model/1 
   *              DELETE - /model/1
   *              POST   - /model
   *             }
   *   services - /service/method/params
   */
  class Controller {
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];
    protected $requestUrl   ='';
    protected $requestMethod ;
    protected $db;
    public $returnType;  //Array or Single
    public $addActionButtons; // Yes or No
    public $table;
    public $post_data;
    public $claim;

    public function __construct(){
      $this->returnType='Array';
      $this->addActionButtons='No';
      $this->db=new Database();  
      
      $this->requestMethod=$_SERVER['REQUEST_METHOD'];
      $this->requestUrl= $this->getUrl();
      $this->post_data = json_decode(file_get_contents('php://input'),true);
            
      $this->resolveUrl();
      $this->table=preg_replace('/[^a-z0-9_]+/i','',$this->requestUrl[0]);
      $this->authenticateRequest();
      //echo getcwd(); 
      $sql='';
      if($this->isCRUDServiceRequest($this->post_data,$this->table)){
           require_once '../app/services/GenericService.php';
      }else{
        if (array_key_exists($this->table, $this->db->models)){ 
            require_once '../app/services/'.$this->table.'Service.php';
        }else if($this->table=='report') {
            require_once '../app/report/rpt_'.$this->requestUrl[1].'.php';
            exit;
        }else{
           if(file_exists('../app/services/'.$this->table.'_Service.php')){
              require_once '../app/services/'.$this->table.'_Service.php';
           }else{  
              // If exists, set as controller)
             echo $this->table;
             header("HTTP/1.1 404 Not Found");
             exit;
            }
        }    
      }
      $this->db->statement=$sql;
      $this->db->execute();
      $this->prepareHTTPResponse();
      exit();
      // Look in BLL for first value ---- ucwords will Capitalize first letter

      if(file_exists('../app/controllers/' . ucwords($url[0]). '.php')){
        // If exists, set as controller
        $this->currentController = ucwords($url[0]);
        // Unset 0 Index
        unset($url[0]);
      }


     
    }

    private function getUrl(){
      if(isset($_GET['url'])){
        $url = rtrim($_GET['url'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);
        return $url;
      }
    }
    private function resolveUrl(){
        
    }
    private function isCRUDServiceRequest($post_data,$table){
      /* This is a CRUD request if  
      GET    url pattern : /table  or /table/id
      PUT    url pattern   /table/id
      POST   url pattern   /table  
      DELETE url Pattern   /table      */ 
      if (array_key_exists($table, $this->db->models)){               
         switch($this->requestMethod){
            case "GET":
              if ((sizeof($this->requestUrl)==2 && is_numeric($this->requestUrl[1])) || sizeof($this->requestUrl)==1 ){
                if(sizeof($this->requestUrl)==1){
                  $this->addActionButtons='Yes';
                }else{
                  $this->returnType='Single';
                }                   
                return true;
              }
              break;
            case "DELETE": 
                if (sizeof($this->requestUrl)==2 && is_numeric($this->requestUrl[1])){
                  return true;
                }
                break;
            case "POST":
                if (sizeof($this->requestUrl)==1 && isset($post_data)){
                  return true;
                }
                break;
            case "PUT":
                if (sizeof($this->requestUrl)==2 && is_numeric($this->requestUrl[1]) && isset($post_data)){
                  return true;
                }
                break;          
          }
          return false;
      }else{
       // header("HTTP/1.1 404 Not Found");
       // exit;
      }
      
    }

    private function prepareHTTPResponse(){
      $result = $this->db->result;
      if (!$result) {
          http_response_code(404);
          die($this->db->statement.'<br>'.mysqli_error($this->db->conn));
      }
       
      if (array_key_exists($this->table, $this->db->models)){       
        $keyfield=$this->db->models->{$this->table}->{"pkey"}; 
       }else {$keyfield='id';}

      $keyvalue='';               
      if ($this->requestMethod=='GET'){
        if($this->returnType=='Array') {  echo '['; }  
        for ($i=0;$i<mysqli_num_rows($this->db->result);$i++) {
            $rec= mysqli_fetch_object($this->db->result);	                      
            $keyvalue=$rec->{trim($keyfield)};
            $rec->id=$keyvalue;
            if($this->addActionButtons=='Yes'){                
              $rec->actions='<button onclick="editThis(\''.$keyvalue.'\',this)" style="margin-left:10px" class="btn btn-primary btn-sm"   data-target="#edit-button" ><i class="glyphicon glyphicon-edit"></i></button>';
              $rec->actions.='<button onclick="deleteThis(\''.$keyvalue.'\',this)" style="margin-left:10px" class="btn btn-danger btn-sm"  data-target="#delete-button" ><i class="glyphicon glyphicon-trash"></i></button>';
            }          
            echo ($i>0?',':'').json_encode($rec);
        }
        if($this->returnType=='Array') {  echo ']'; }
        elseif (mysqli_num_rows($this->db->result)==0) echo '{}';
      
      } elseif ($this->requestMethod == 'POST') {
      echo mysqli_insert_id($this->db->conn);
      } else {
      echo mysqli_affected_rows($this->db->conn);
      }
      
      // close mysql connection
      mysqli_close($this->db->conn);

    }
    private function authenticateRequestx(){
      if ($this->table=='login'){
                if (login($this->db->conn) == true) {
                    exit('Login success');
                } else {
                  //  echo ('<br><br>login fail'. $this->db->conn->error);
                    header("HTTP/1.1 401 Unauthorized");
                    exit;
                }
          }else{
            $this->claims=authenticateToken();
            if ($this->claims==null){
                    header("HTTP/1.1 401 Unauthorized");
                    exit('invalid token');
            }
          }
    }

    private function authenticateRequest(){
      if ($this->table=='login'){
            if (login($this->db->conn) == true) {
                exit('Login success');
            } else {
              //  echo ('<br><br>login fail'. $this->db->conn->error);
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
          }else{
            //echo ($this->requestMethod);
            $this->claims=authenticateToken();
            if ($this->claims==null){
                    header("HTTP/1.1 401 Unauthorized");
                    exit('invalid token');
            }
          }
          if ($this->table=='update'){
              if (updateUser($this->db->conn) == true) {
                  exit('Update success');
              } else {
                //  echo ('<br><br>login fail'. $this->db->conn->error);
                  header("HTTP/1.1 404 Not Found");
                  exit;
              }
            }
            if ($this->table=='change_password'){
                if (changePasswprd($this->db->conn) != null) {
                    exit('Update success');
                } else {
                  //  echo ('<br><br>login fail'. $this->db->conn->error);
                    header("HTTP/1.1 404 Not Found");
                    exit;
                }
              }
              if ($this->table=='new'){
                if (createUser($this->db->conn) == true) {
                    exit;
                } else {
                  //  echo ('<br><br>login fail'. $this->db->conn->error);
                    header("HTTP/1.1 404 Not Found");
                    exit;
                }
              }

            
              
    
    }

  } 