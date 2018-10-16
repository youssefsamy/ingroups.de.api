<!DOCTYPE html>
<html>
<head lang='en' style='font-family: Open Sans, Helvetica;'>
    <meta charset='UTF-8' style='font-family: Open Sans, Helvetica;'>
    <title style='font-family: Open Sans, Helvetica;'>Event Contact Verification</title>
    <style style='font-family: Open Sans, Helvetica;'>
        * {
            font-family: Open Sans, Helvetica;
        }

        body {
            padding: 10px;
            margin: 0px auto;
            width: 620px;

        }

        #header {

            border-bottom: 2px solid #888;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        #header > img {
            width: 80px;
        }

         #header>a > img {
            width: 80px;
        }
        #header > #menu {
            float: right;
            margin-top: 12px;
        }

        #header > #menu > a {
        }

        #header > #menu > a > img {
            width: 32px;
            height: 32px;
        }

        body > div {
            max-width: 600px;
        }

        #Data {
            vertical-align: top;
        }

        #ContactBox {

            padding-bottom: 4px;
            border: 1px solid #428BCA;
            border-radius: 4px;
            box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.05);
        }

        #ContactBox h3 {
            color: #FFF;
            margin: 0px;
        }

        #ContactBox > div > div {
            display: inline-block;
        }

        #ContactHead {

            padding: 10px 20px;

            background-color: #428BCA;
            border-bottom: 1px solid #428BCA;
            border-top-right-radius: 3px;
            border-top-left-radius: 3px;
        }

        #ContactBody {
            margin: 0px 20px;
        }

        #MessageBox {
            margin-top: 20px;;
            padding: 4px 20px;
            background: none repeat scroll 0% 0% #434A54;
            border-radius: 4px;
        }

        #MessageBox h3 {
            color: #AAB2BD;
        }

        #MessageBox p {
            color: #FFF;
        }

        .button {
            float: right;
            margin: 12px 0px;
            text-decoration: none;
            font-weight: 500;
            background-color: #5CB85C;
            border-color: #4CAE4C;
            color: #FFF;
            padding: 10px 12px;
            padding-top: 11px;
            text-align: center;
            border-radius: 4px;
        }

        .button:hover {
            background-color: #418141;
            border-color: #418141;
        }
        
        #del_button {
            float: left;
        }
        #eventImg {
            width: 100%;
            height: auto;
            margin-bottom: 14px;
            max-width: 600px;
        }
    </style>
    </head>
<body style='font-family: Open Sans, Helvetica;padding: 10px;margin: 0px auto;width: 95%;'>
<div id='header' style='font-family: Open Sans, Helvetica;max-width: 600px;border-bottom: 2px solid #888;padding-bottom: 10px;margin-bottom: 12px;'>
    <a href='http://ingroups.de/' style='font-family: Open Sans, Helvetica;'><img alt='' src='https://ingroups.de/storage/email_logo.png' style='font-family: Open Sans, Helvetica;width: 320px;margin-top: 12px;'>
    </a>

    <div id='menu' style='font-family: Open Sans, Helvetica;float: right;margin-top: 12px;'>
    <a href=''  style='font-family: Open Sans, Helvetica;'><img alt='' src='https://ingroups.de/storage/Facebook.png' style='font-family: Open Sans, Helvetica;width: 32px;height: 32px;'></a>
        <a href=''  style='font-family: Open Sans, Helvetica;'><img alt='' src='https://ingroups.de/storage/Twitter.png' style='font-family: Open Sans, Helvetica;width: 32px;height: 32px;'></a>
        <a href=''  style='font-family: Open Sans, Helvetica;'><img alt='' src='https://ingroups.de/storage/Google-Plus.png' style='font-family: Open Sans, Helvetica;width: 32px;height: 32px;'></a>
    </div>
</div>
<h3 style='font-family: Open Sans, Helvetica;'>Danke, dass du InGroups nutzt.</h3>
<h3 style='margin-top: 20px;font-family: Open Sans, Helvetica;'>Dein Event:</h3>
<img alt='' id='eventImg' src='{{$event_info['image']}}' style='font-family: Open Sans, Helvetica;width: 100%;height: auto;margin-bottom: 14px;max-width: 600px;'>

<div id='Data' style='font-family: Open Sans, Helvetica;max-width: 600px;vertical-align: top;'>
    <div id='ContactBox' style='font-family: Open Sans, Helvetica;padding-bottom: 4px;border: 1px solid #428BCA;border-radius: 4px;box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.05);'>
        <div id='ContactHead' style='font-family: Open Sans, Helvetica;padding: 10px 20px;background-color: #428BCA;border-bottom: 1px solid #428BCA;border-top-right-radius: 3px;border-top-left-radius: 3px;'>
            <h3 style='font-family: Open Sans, Helvetica;color: #FFF;margin: 0px;'>Daten</h3>
        </div>
        <div id='ContactBody' style='font-family: Open Sans, Helvetica;margin: 0px 20px;'>
            <div style='font-family: Open Sans, Helvetica;display: inline-block;'>
                <p style='font-family: Open Sans, Helvetica;'>Titel: {{$event_info['Titel']}}</p>
                <p style='font-family: Open Sans, Helvetica;'>Datum: {{$event_info['Datum']}}</p>
            </div>
        </div>
    </div>
    <div id='MessageBox' style='font-family: Open Sans, Helvetica;margin-top: 20px;padding: 4px 20px;background: none repeat scroll 0% 0% #434A54;border-radius: 4px;'>
        <h3 style='font-family: Open Sans, Helvetica;color: #AAB2BD;'>Beschreibung:</h3>

        <p style='font-family: Open Sans, Helvetica;color: #FFF;'>{{$event_info['Beschreibung']}}</p>
    </div>
</div>
</body>
</html>
