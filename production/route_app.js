(function ($) {

  var app = $.sammy('#app', function () {
    this.use('Template');

    this.around(function (callback) {
      var context = this;
      this.load('data/articles.json')
        .then(function (items) {
          context.items = items;
        })
        .then(callback);
    });

    this.get('#/', function (context) {
      context.app.swap('');
      $.each(this.items, function (i, item) {
        context.render('templates/article.template', { id: i, item: item })
          .appendTo(context.$element());
      });
    });

    this.get('#/login/', function (context) {
      context.app.swap('');alert('trying to loginf');
      $.each(this.items, function (i, item) {
        context.render('templates/login.template.htmml', { id: i, item: item })
          .appendTo(context.$element());
      });
    });


    this.get('#/about/', function (context) {
      var str = location.href.toLowerCase();
      context.app.swap('');
      context.render('templates/about.template', {})
        .appendTo(context.$element());
        $.getScript("templates/js/login.js", function (data, textStatus, jqxhr) {
          console.log(jqxhr.status); // 200
          console.log("Load was performed.");
        }).fail(function( jqxhr, settings, exception ) {
         alert(exception);
      });
    });

    this.get('#/commodity/', function (context) {
      var str = location.href.toLowerCase();
      context.app.swap('');alert('trying to loginf dd');
      context.render('templates/commodity.template.htmlx', {})
        .appendTo(context.$element());
      $.getScript("templates/js/commodity.js", function (data, textStatus, jqxhr) {
        //console.log(data); // Data returned
        //console.log(textStatus); // Success
        console.log(jqxhr.status); // 200
        console.log("Load was performed.");
      }).fail(function( jqxhr, settings, exception ) {
       alert(exception);
    });
    });

    this.get('#/article/:id', function (context) {
      this.item = this.items[this.params['id']];
      if (!this.item) { return this.notFound(); }
      this.partial('templates/article-detail.template');
    });


    this.before('.*', function () {

      var hash = document.location.hash;
      $("nav").find("a").removeClass("current");
      $("nav").find("a[href='" + hash + "']").addClass("current");
    });

  });

  $(function () {
    app.run('#/about/');
  });


})(jQuery);
