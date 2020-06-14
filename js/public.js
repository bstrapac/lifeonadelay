var public = angular.module("public", ["ngRoute", "ngCookies"]);

public.config(function($routeProvider){
    $routeProvider
   .when("/",{
        templateUrl:"templates/public.html",
        controller: "cntrl"
   })
    .when("/post_single/:post_id",{
       templateUrl:"templates/post_single.html",
       controller: "cntrl"
   })
   .when("/authors",{
       templateUrl:"templates/authors.html",
       controller: "cntrl"
   })
   .otherwise({
       redirectTo: "/"
   });
});
public.controller("cntrl", function($scope, $routeParams, $http, $route){
    //FUNKCIJE ZA MANIPULACIJU POSTOVIMA
    //load početne stranice, dohvaća se zadnjih 5 postova
    $scope.posts = [];
	$scope.LoadPosts = function(){
		$http({
			method: 'GET',
			url: 'json.php?json_id=get_last_posts'
		}).then(function(response) {
            $scope.posts = response.data;
		}, function(e) {
			console.log(e);
		});
    };
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
    $scope.authors = [];
    $scope.LoadAllUsers = function(){
        $http({
            method: 'GET',
            url: 'json.php?json_id=get_users'
        }).then(function(response){
            $scope.authors = response.data;
        }, function(e){
            console.log(e);
        });
    };
    $scope.ShowUser = function(){
        $http({
            method: 'GET',
            url: 'json.php?json_id=get_user&name='+$scope.author
        }).then(function(response){
            $scope.author_name = response.data.firstname;
            $scope.author_lastname = response.data.lastname;
        }, function(e){
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
    //FUNKCIJE ZA MANIPULACIJU KOMENTARIMA
    //loadanje svih komentara odabranog posta, proslijeđeni post_id smo dodali kao parametar funkcije kako bi
    //se učitali komentari samo odabranog posta
    $scope.comments = [];
    $scope.LoadComments = function(post_id){
        var postData = {
            'action_id': 'get_comments',
            'post_id': post_id
        }
        $http.post('action.php', postData).then
        (function(response){
            $scope.comments = response.data;
            //console.log(response.data);
        }, function(e){
            console.log(e);
        });
    };
    //dodavanje novog komentara na odabrani post, timestamp se dodaje u procc preko GETDATE()
    $scope.AddNewComment = function(post_id){
        var postData= {
            'action_id': 'post_comment',
            'username': $scope.nickname,
            'comment': $scope.new_comment,
            'post_id': post_id
        }
        $http.post('action.php', postData).then
        (function(response){
            $route.reload();
        },
        function(e){
            console.log(e);
        });
    };
    $scope.DeleteComment = function( comment_id ){
        var postData = {
            'action_id' : 'delete_comment',
            'comment_id' : comment_id
        }
        $http.post('action.php', postData).then
        (function(response){
            //console.log(comment_id);
            $route.reload();
        },
        function(e){
            console.log(e);
        });
    };
});

//JS funkcije XSS primjeri
$(document).ready(function(){
    $(".btn_search").click(function(){
        var user_name = $(".author").val();
        const url= 'json.php?json_id=get_user&name='+user_name;
        console.log(user_name);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(result){
                $(".searched_info").html("Pretražili ste: " + user_name);
                $(".result").text(result.firstname +" "+ result.lastname);
                console.log(result);
            },
            error: function(e){
                console.log(e);
            }
        });
    });

    $(".load").click(function LoadComment(){
        console.log('fja');
        const url = 'action.php';
        const action_id ='get_comments';
        const post_id = 1;
        $.post( url, 
            {
                action_id : action_id,
                post_id: post_id
            }, 
            function(result){
                console.log(result);
                $.each(result, function(){
                    console.log(this);
                    $(".comment_author").html(this.username);
                    $(".comment_content").html(this.content);
                });                
            }, 
            'json'
        );
    });
});