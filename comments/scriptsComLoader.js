var socNetName = undefined;
var fb_link = "https://connect.facebook.net/ru_RU/sdk.js";
var vk_link = "https://vk.com/js/api/openapi.js";
var ok_link = "//api.ok.ru/js/fapi5.js";
var baseScripts = [
    "http://www.bsmu.by/scripts/upper.js",
    fb_link,
    vk_link,
    ok_link,
    "/comments/ajax_funcLib.js",
    "/comments/ajax_control.js",
    "/comments/googleAuth.js"
];

//Проверка статуса логина соцсетей
var cookiePrefix = "up_key_";
var activeCookie = cookiesIsSet(cookiePrefix);
//socNetName = activeCookie.replace(cookiePrefix, "");
//(activeCookie != undefined) ? baseScripts = apiChange(activeCookie.replace(cookiePrefix, ""), baseScripts) :
//    baseScripts = apiChange(null, baseScripts);

//Загрузка скриптов
loadScripts(baseScripts);


//переключение апи
function apiChange(socID, baseScripts) {
    switch (socID) {
        case 'vk':
        {
            baseScripts.push(vk_link);
            socNetName = "vk";
        }
            break;
        case 'fb':
        {
            baseScripts.push(fb_link);
            socNetName = "fb";
        }
            break;
        default:
        {
            socNetName = undefined;
        }
            break;
    }
    return baseScripts;
}

function loadScripts(scripts_ex, callback) {
    var scripts = scripts_ex;
    var src;
    var script;
    var pendingScripts = [];
    var firstScript = document.scripts[0];

    function loader() {
        // loop through our script urls
        if ('async' in firstScript) { // modern browsers
            script = document.createElement('script');
            script.async = false;
            script.src = src;
            document.head.appendChild(script);
        } else if (firstScript.readyState) { // IE<10
            // create a script and add it to our todo pile
            script = document.createElement('script');
            pendingScripts.push(script);
            // listen for state changes
            script.onreadystatechange = stateChange;
            // must set src AFTER adding onreadystatechange listener
            // else we’ll miss the loaded event for cached scripts
            script.src = src;
        } else { // fall back to defer
            document.write('<script src="' + src + '" defer></' + 'script>');
        }
    }

    // Watch scripts load in IE
    function stateChange() {
        // Execute as many scripts in order as we can
        var pendingScript;
        while (pendingScripts[0] && pendingScripts[0].readyState == 'loaded') {
            pendingScript = pendingScripts.shift();
            // avoid future loading events from this script (eg, if src changes)
            pendingScript.onreadystatechange = null;
            // can't just appendChild, old IE bug if element isn't closed
            firstScript.parentNode.insertBefore(pendingScript, firstScript);
        }
    }

    while (scripts.length != 0) {
        src = scripts.shift();
        loader();
    }
    if(callback != undefined){
        callback();
    }

}

function cookiesIsSet(name_base) {
    var matches = document.cookie.match(new RegExp(name_base + "\\w{1,}"));
    return matches ? matches[0] : undefined;
}