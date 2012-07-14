<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>[[404 - Not found]]</title>
    <base href="{$baseUrl}" />
    <style type="text/css">
        body {
            color: white;
            line-height: 150%;
            font-size: 13px;
            margin: 0px;
            font-family: Verdana, Sans;
            background-color: #22628d;
        }

        #error {
            margin: 5px 50px;
            padding: 5px 45px;
            text-align: left;
        }

        #error > h2 {
            margin-top: 50px;
            color: red;
        }

        .msg {
            color: #444;
            white-space: pre;
            overflow-x: auto;
            background-color: #f7f7f7;
            padding: 15px;
            border-top: 3px solid red;
            border-bottom: 3px solid red;
        }

    </style>
</head>
<body>
<div id="error">
    <img src="media/Kryn/images/logo_white.png" />
    <h2>{$title}</h2>
    <div class="msg">{$msg}
    </div>
</div>
<script type="text/javascript" src="media/Kryn/js/bgNoise.js"></script>
</body>
</html>