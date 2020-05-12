var admin = angular.module("admin", ["ngRoute", "ngCookies"]);

admin.config(function($routeProvider){
    $routeProvider
   .when("/",{
        templateUrl:"templates/admin_main.html",
        controller: "admin_cntrl"
   })
   .when("/edit_post/:post_id",{
    templateUrl:"templates/edit_post.html",
    controller: "admin_cntrl"
    })
    .when("/add_new",{
        templateUrl:"templates/new_post.html",
        controller: "admin_cntrl"
    })
    .when("/pending_users",{
        templateUrl:"templates/users.html",
        controller: "admin_cntrl"
    })
    .when("/login",{
        templateUrl:"templates/login.html",
        controller: "admin_cntrl"
    })
    .when("/register",{
        templateUrl:"templates/register.html",
        controller: "admin_cntrl"
    });
});

admin.directive("fileInput", function($parse){
    return{
        link: function($scope, element, attrs){
            element.on("change", function(event){
                var files = event.target.files;
                $parse(attrs.fileInput).assign($scope, element[0].files);
                $scope.$apply();
            });
        }
    }
});

admin.factory('Authentication', function( $cookies ){
	var auth = {};
	auth.GetLoginStatus = function(){
		if( $cookies.get('logged_user')){
			return true;
		}
		else{
			return false;
		}
	}
	auth.SetLoggedUser = function( LoggedUser){
		$cookies.put('logged_user', LoggedUser)
	}
	auth.Logout = function(){
		$cookies.remove('logged_user')
	}
	return auth;
});
admin.controller("admin_cntrl", function($scope, $routeParams, $http, $route, $location, $cookies, Authentication ){
    $scope.passParam = function(param){
        $scope.paramPassed = param;
    };
    $scope.img = '';
    $scope.uploadFile = function(){
        var postdata = new FormData();
        angular.forEach($scope.files, function(file){
            postdata.append('file', file);
        })
       $http.post('upload.php', postdata, {
            transformRequest: angular.identity, 
            headers :{'Content-Type': undefined, 'Process-Data': false}
        }).then(function(response){
            $scope.img = response.data;
            console.log($scope.img);
        });
    };
    $scope.ulogiran = false;
	if( Authentication.GetLoginStatus() == false ){
		$location.path('/login');
	}
	$http.post('action.php', {action_id: 'check_logged_in'})
	    .then(function (response){
		    	if( response.data == 1 ){
		    		$scope.ulogiran = true;
		    	}
		    	else{
		    		$scope.ulogiran = false;
		    		$location.path('/login');
		    	}
		    },function (e){
		    	console.log('error');
	    		$scope.ulogiran = false;
		 	}
    );
    $scope.user = [];
    $scope.Login = function(){
        var postData={
            'action_id' : 'login',
            'email' : $scope.email,
            'password' : $scope.password
        };
        $http.post('action.php', postData).then
        (function(response){
            if( response.data[1] == 1 )
		    	{
                    $scope.ulogiran = true;
                    $scope.user = response.data[0];
		    		Authentication.SetLoggedUser($scope.user.id);
                    $location.path('/');
                    console.log( $scope.user.id);
		    	}
		    	else
		    	{
		    		$location.path('/login');
		    		alert('Netočni podaci. Pokušajte ponovno');
		    	}
        }, function (e){
            console.log(e);
        });
    };
    $scope.Logout = function(){
        var postData = {
            'action_id' : 'logout'
        };
        $http.post('action.php', postData).then
        (function(response){
            //console.log(response.data);
            $location.path('/login');
        }, function(e){
            console.log(e);
        });
    };
    $scope.Register = function(){
        var postData = {
            'action_id' : 'register',
            'username' : $scope.username,
            'firstname' : $scope.firstname,
            'lastname' : $scope.lastname,
            'email' : $scope.email_reg,
            'password' : $scope.password_reg,
            'phone' : $scope.phone,
            'birth_date' : $scope.birth_date
        };
        $http.post('action.php', postData).then
        (function(response){
            //console.log(response.data);
        }, function(e){
            console.log(e);
        });
    };
    //FUNKCIJE ZA MANIPULACIJU POSTOVIMA
    $scope.posts = [];
    $scope.LoadAllPosts = function(){
        $http({
            method: 'GET',
            url: 'json.php?json_id=get_all_posts'
        }).then(function(response){
            $scope.posts = response.data;
        }, function(e){
            console.log(e);
        });
    };
    $scope.LoadAllUsersPosts = function(user_id){
        var postData = {
            'action_id' : 'get_all_user_posts',
            'user_id' : user_id
        }
        $http.post('action.php', postData).then
        (function(response){
            $scope.posts = response.data;
        }, function(e){
            console.log(e);
        });
    };
    $scope.post = [];
    $scope.post_id = $routeParams.post_id;
    $scope.LoadPost = function(post_id){
        var postData = {
            'action_id' : 'get_single_post',
            'post_id' : post_id
        }
        $http.post('action.php', postData).then
        (function(response){
            $scope.post = response.data;
        }, function(e){
            console.log(e);
        });
    };
    $scope.EditPost = function(post_id){
        var new_title = angular.element( document.querySelector( '.new_title' ) );
        var new_content = angular.element( document.querySelector( '.new_content' ) );
        var postData ={
            'action_id' : 'edit_post',
            'post_id' : post_id,
            'new_title' : new_title.val(),
            'new_content' : new_content.val()
        }
        $http.post('action.php', postData).then
        (function(response){
        }, function(e){
            console.log(e);
        });
    };
    $scope.AddNewPost = function(){
        //console.log($scope.img);
        var postData= {
            'action_id': 'add_post',
            'title' : $scope.new_title,
            'content' : $scope.new_content,
            'image' : $scope.img,
            'user_id' : $cookies.get('logged_user')
        }
        $http.post('action.php', postData).then
        (function(response){
            $location.path('/');
        },
        function(e){
            console.log(e);
        });
    };
    $scope.DeletePost = function( post_id ){
        var postData = {
            'action_id' : 'delete_post',
            'post_id' : post_id
        }
        $http.post('action.php', postData).then
        (function(response){
            $route.reload();
        },
        function(e){
            console.log(e);
        });
    };
    //load single posta, parametar koji se proslijeđuje sa početne stranice putem url
    //sprema se u varijblu post_id preko routeParams, param. u url smo dodali na link koji vodi
    //na sinle post preview i dohvatili ga ispod, zatim smo ga iskoristili koa parametar fj-e u single post
    //ng-init funkciji 
    $scope.post = [];
    $scope.post_id = $routeParams.post_id;
    $scope.LoadPost = function(post_id){
        var postData = {
            'action_id' : 'get_single_post',
            'post_id' : post_id
        }
        $http.post('action.php', postData).then
        (function(response){
            $scope.post = response.data;
            //console.log($scope.post);
        }, function(e){
            console.log(e);
        });
    };
    //FUNKCIJE ZA KORISNIKE
    $scope.users = [];
    $scope.LoadPendingUsers = function(){
        $http({
			method: 'GET',
			url: 'json.php?json_id=get_pending_users'
		}).then(function(response) {
            $scope.users = response.data;
            //console.log(response.data);
		}, function(e) {
			console.log(e);
		});
    };
    $scope.LoadUsers = function(){
        $http({
			method: 'GET',
			url: 'json.php?json_id=get_users'
		}).then(function(response) {
            $scope.users = response.data;
            //console.log(response.data);
		}, function(e) {
			console.log(e);
		});
    };
    $scope.ApprovePendingUser = function(user_id){
        var postData = {
            'action_id' : 'approve_user',
            'user_id' : user_id
        };
        $http.post('action.php', postData).then
        (function(response){
            console.log(response.data);
        }, function(e){
            console.log(e);
        });
    };
    $scope.DeletePendingUser = function(user_id){
        var postData ={
            'action_id' : 'delete_pending_user',
            'user_id' : user_id
        };
        $http.post('action.php', postData).then
        (function(response){
            console.log(response.data);
        }, function(e){
            console.log(e);
        });
    };
});