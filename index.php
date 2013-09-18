<html>
<head>
<script src="jquery-2.0.3.min.js"></script>
<meta charset=utf-8 />
<title></title>


</head>
<body>

  <div class="lists-holder"></div>
  <div class="list-holder"></div> 
</body>
</html>

<script>

  var launcherId = 'simon';
  var liveListId = -1;

  $(document).ready(function(){
    refreshRevisionList(function() {
      refreshAppList(liveListId);
    });
  });

    var refreshRevisionList = function(callback) {
      $.ajax({
      url: 'api/com/?req={"ListRevisionReq":{"launcherId":"' + launcherId +'"}}',
      context: document.body
      }).done(function(result) {
       var data = $.parseJSON(result);
       var lists = data.ListRevisionResp.result.lists;
       liveListId = data.ListRevisionResp.result.liveList;
       var editRevisionExists = false;
       $(".lists-holder").html('');
      
       var editRevisionExists = false; 
       $(lists).each(function(index, list) {
         if('1' != list.has_been_live) {
           editRevisionExists = true;
         }
         var listItem = listItemHtml(list);
         $(".lists-holder").append(listItem);
       });

       $(".list-item").click(listsItemAction);


       if(!editRevisionExists) {
         var buttonCreateRevision =
         '<input type="button" class="button-create-revision" value="Edit" data="' + liveListId + '">';
         $(".lists-holder").append(buttonCreateRevision);
         $(".button-create-revision").click(createRevisionAction);
       }
       if(callback) {
        callback.apply(data);
       }
     });
   };

    var listItemHtml = function(list) {
      var buttonText = list.changed + ' revision ' + list.id;
      if(liveListId == list.id) {
        buttonText += ' [LIVE]';
      } else if('1' == list.has_been_live) {
        buttonText += ' [WAS LIVE]';
      } else {
        buttonText += ' [EDIT]';
      }
      var listItem =
       '<input type="button" class="list-item" value="' + buttonText + '" data="' + list.id + '">';
     return listItem;
    };

    var listsItemAction = function() {
      var revision = $(this).attr('data');
      refreshAppList(revision);
    };

    var createRevisionAction = function() {
      var revision = $(this).attr('data');
      var url = 'api/com/?req={"NewListRevisionReq":{"launcherId":"' + launcherId + '","baseList":"' + revision + '"}}';
      $.ajax({
      url: url,
      context: document.body
      }).done(function(result) {
        var obj = $.parseJSON(result);
        refreshRevisionList();
        var newListId = obj.NewListRevisionResp.result.list_id;
        if(newListId) {
          refreshAppList(newListId);
        }
      });    
    };

    var refreshAppList = function(revision) {
      var url = 'api/com/?req={"ListReq":{"launcherId":"' + launcherId + '","revision":"' + revision + '"}}';
      $.ajax({
      url: url,
      context: document.body
      }).done(function(result) {
        var obj = $.parseJSON(result);
        $(".list-holder").html(listHtml(obj.ListResp));
        if(obj.ListResp.hasBeenLive) {
          $(".list-holder").find(":input").prop("disabled", true);
        }
        $(".button-add-app").click(addAppAction);
        $(".button-delete-app").click(deleteAppAction);
        $(".button-delete-list").click(deleteListAction);
        $(".button-publish-list").click(publishListAction);
      });
    };

    var deleteListAction = function() {
      $(this).prop('disabled', true);
      var revision = $(this).closest(".app-list").attr('data');
      
      var req = {};
      var data = {};
      data.launcherId = launcherId;
      data.revision = revision;    
      req.DeleteListRevisionReq = data;
      var reqStr = JSON.stringify(req);
      var url = 'api/com/?req=' + reqStr;

      $.ajax({
      url: url,
      context: document.body
      }).done(function(result) {
        refreshRevisionList();
        refreshAppList(liveListId);
      });
    }

    var publishListAction = function() {
      $(this).prop('disabled', true);
      var revision = $(this).closest(".app-list").attr('data');

      var req = {};
      var data = {};
      data.launcherId = launcherId;
      data.appListId = revision;
      req.PublishListReq = data;
      var reqStr = JSON.stringify(req);
      var url = 'api/com/?req=' + reqStr;

      $.ajax({
      url: url,
      context: document.body
      }).done(function(result) {
        refreshRevisionList();
        refreshAppList(revision);
      });
    }


    var addAppAction = function() {
      $(this).prop('disabled', true);
      var appId = $(this).parent().find(".input-app-id").val();
      var revision = $(this).closest(".app-list").attr('data');
      var position = $(this).parent().parent().find(".input-position").val();
      var latitude = $(this).parent().parent().find(".input-latitude").val();
      var longitude = $(this).parent().parent().find(".input-longitude").val();

      var req = {};
      var AddToListReq = {};
      AddToListReq.appId = appId;
      AddToListReq.appListId = revision;
      AddToListReq.position = position;
      if('' != latitude) {
        AddToListReq.latitude = latitude;
      }
      if('' != longitude) {
        AddToListReq.longitude = longitude;
      }
 
      req.AddToListReq = AddToListReq;
      var reqStr = JSON.stringify(req);

      var url = 'api/com/?req=' + reqStr;

      $.ajax({
      url: url,
      context: document.body
      }).done(function(result) {
        refreshAppList(revision);
      });
    };

    var deleteAppAction = function() {
      $(this).prop('disabled', true);
      var appId = $(this).parent().find(".input-app-id").val();
      var appListId = $(this).closest(".app-list").attr('data');
      var url = 'api/com/?req={"RemoveFromListReq":{"appId":"' + appId + '","appListId":' +appListId + '}}';
      $.ajax({
      url: url,
      context: document.body
      }).done(function(result) {
        refreshAppList(appListId);
      });
    }

    var listHtml = function(appListResp){
      var appList = appListResp.tile;
      var revision = appListResp.revision;  
      var result = '<ul class="app-list" data="' + revision + '">';
      result += '<input type="button" class="button-delete-list" value="Discard">';
      result += '<input type="button" class="button-publish-list" value="Publish">';
      $(appList).each(function(index, item) {
        var app = item.appTile;

        if(typeof(app.latitude) == 'undefined') {
          app.latitude = '';
        }

        if(typeof(app.longitude) == 'undefined') {
          app.longitude = '';
        }
 
        result += '<li>';
        result += '<div class="title-holder">' + app.title + '</div>';
        result += '<div class="developer-holder">' + app.developer + '</div>';
        result += '<div class="icon-holder"><img src="' + app.iconUri +'=w64"></div>';
        result += '<div class="position-holder">';
        result += '<input type="text" class="input-position" value="' + app.position  + '">';
        result += '</div>';
        result += '<div class="geolocation-holder">';
        result += '<input type="text" class="input-latitude" value="' + app.latitude + '" placeholder="latitude">';
        result += '<input type="text" class="input-longitude" value="' + app.longitude + '" placeholder="longitude">';
        result += '</div>';
 result += '<div class="update-app-holder">';
result += '<input type="text" class="input-app-id" placeholder="package name" disabled value="' + app.packageName  + '">';
      result += '<input type="button" class="button-add-app" value="Update">';
result += '<input type="button" class="button-delete-app" value="X">';

        result += '</div>';

        result += '</li>';

      });
 
      result += '<li>';
      result += '<div class="add-app-holder">';
      result += '<input type="text" class="input-app-id" placeholder="package name">';
      result += '<input type="button" class="button-add-app" value="Add app">';
      result += '</div>';
 
      result += '</ul>';
      return result;
    };
</script>

