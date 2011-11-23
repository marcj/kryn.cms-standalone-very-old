<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>[[404 - Not found]]</title>
        <base href="{$baseUrl}" />
        <style type="text/css">
            body {
                color: #444;
                line-height: 150%;
                font-size: 13px;
                margin: 0px;
                text-align: center;
                font-family: Verdana, Sans;
            }
            
            #error {
                width: 600px;
                margin: 0px;
                border: 1px solid silver;
                padding: 45px;
                -moz-border-radius: 4px;
                -webkit-border-radius: 4px;
                border-radius: 4px;
                margin: auto;
                text-align: left;
                margin-top: 50px;
                background-color: #eee;
            }
            
            .desc {
                color: gray;
                padding-left: 13px;
            }
            
        </style>
    </head>
    <body>
        <div id="error">
            <h2>[[Internal system error]]</h2>
            <br />
            <div>
                {$msg}
            </div>
        </div>
    </body>
</html>