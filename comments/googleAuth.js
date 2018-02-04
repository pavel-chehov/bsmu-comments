gooClientID = "1060114582002-giddsvfq6rg7tdjhoqcp3p9d73672mvc.apps.googleusercontent.com";
//Вставьте асинхронный код JavaScript прямо перед тегом </body>
(function() {
    var po = document.createElement('script');
    po.type = 'text/javascript';
    po.async = true;
    po.src = 'https://apis.google.com/js/client:plusone.js?onload=render';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
})();

function signinCallback(authResult) {
    if (authResult['access_token']) {
        // Успешная авторизация
        // Скрыть кнопку входа после авторизации пользователя, например:
        // document.getElementById('signinButton').setAttribute('style', 'display: none');
        var params = "access_token=" + authResult['access_token'];
        insertNewData(params, '../setGooCookie.php', null, 'POST', function(ret) {
            getLoginStatusForAll();
        });

        gapi.auth.setToken(authResult); // Сохраняет возвращенный токен.
        gapi.client.load('oauth2', 'v2', function() {
            var request = gapi.client.oauth2.userinfo.get();
            request.execute(function(obj) {
                var user = obj;
            });
        });
        var params = "client_id=" + authResult['client_id'] + "&pageUrl=" + window.location;
        //insertNewData(params,'../setGooCookie.php', 'user_info', 'POST', null);
    } else if (authResult['error']) {
        // Произошла ошибка.
        // Возможные коды ошибок:
        //   "access_denied" – пользователь отказался предоставить приложению доступ к данным
        //   "immediate_failed" – не удалось выполнить автоматический вход пользователя
        console.log('There was an error: ' + authResult['error']);
    }
}

function render() {
    gapi.load('auth2', function() {
        gapi.auth2.init();
    });
    gapi.signin.render('customBtn', {
        'callback': 'signinCallback',
        'clientid': gooClientID,
        'cookiepolicy': 'http://comments.akson.by',
        'requestvisibleactions': 'http://schemas.google.com/AddActivity',
        'scope': 'https://www.googleapis.com/auth/plus.login',
        'approvalprompt': "force"
    });

}

function GooglePlusLogOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function() {
        console.log('User signed out.');
    });
}

function GooglePlusLogIn() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signIn();
}