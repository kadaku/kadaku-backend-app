<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Verifikasi Email</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
  <style>
    html, body, div, span, applet, object, iframe,
    h1, h2, h3, h4, h5, h6, p, blockquote, pre,
    a, abbr, acronym, address, big, cite, code,
    del, dfn, em, img, ins, kbd, q, s, samp,
    small, strike, strong, sub, sup, tt, var,
    b, u, i, center,
    dl, dt, dd, ol, ul, li,
    fieldset, form, label, legend,
    table, caption, tbody, tfoot, thead, tr, th, td,
    article, aside, canvas, details, embed, 
    figure, figcaption, footer, header, hgroup, 
    menu, nav, output, ruby, section, summary,
    time, mark, audio, video {
      margin: 0;
      padding: 0;
      border: 0;
      font-size: 100%;
      font: inherit;
      vertical-align: baseline;
    }
    /* HTML5 display-role reset for older browsers */
    article, aside, details, figcaption, figure, 
    footer, header, hgroup, menu, nav, section {
      display: block;
    }
    body {
      line-height: 1;
      color: rgb(48, 48, 48);
    }
    ol, ul {
      list-style: none;
    }
    blockquote, q {
      quotes: none;
    }
    blockquote:before, blockquote:after,
    q:before, q:after {
      content: '';
      content: none;
    }
    table {
      border-collapse: collapse;
      border-spacing: 0;
    }

    .container {
      position: relative;
      width: 65%;
      margin: auto;
    }

    .card {
      margin: auto;
      margin-top: -200px;
      border-radius: 15px;
      text-align: center;
      padding: 3rem;
      background-color: white;
      z-index: 2;
    }

    .dont-break-out {
      /* These are technically the same, but use both */
      overflow-wrap: break-word;
      word-wrap: break-word;
      -ms-word-break: break-all;
      /* This is the dangerous one in WebKit, as it breaks things wherever */
      word-break: break-all;
      /* Instead use this non-standard one: */
      word-break: break-word;
      /* Adds a hyphen where the word breaks, if supported (No Blink) */
      -ms-hyphens: auto;
      -moz-hyphens: auto;
      -webkit-hyphens: auto;
      hyphens: auto;
    }

    @media screen and (max-width: 600px) {
      .container {
        width: 90%;
      }
    }
  </style>
</head>
<body style="width: 100%;height: 100%;font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif ;background-color: #eee;">
  <div style="width: 100%;height: 250px; position: relative;background-color: #FDD991;z-index: 0;border-bottom-left-radius: 80px;border-bottom-right-radius: 80px;"></div>
  <div class="container"> 
    <div class="card">
      <img src="https://ws.kadaku.id/storage/images/brand/brand-1681398871-3ad879a2288ad0d5c138df60185ef7fcec41d7bb.png" width="60" style="margin-bottom: 40px;" alt="">
      <h1 style="font-size: 20px;font-weight: 700;margin-bottom: 15px;">Reset Your Password</h1>
      <hr style="border-style: dashed;border-top: 0.9px;border-color: lightgray">
      <h6 style="font-size: 14px">Hello, <span style="font-weight: 700;">{{ isset($name) ? $name : '' }}</span></h6>
      <hr style="border-style: dashed;border-top: 0.9px;border-color: lightgray">
      <p style="font-size: 14px;margin-top:20px;">We received a password reset request from your account. Click the button below to continue...</p>
      
      <a href="{{ isset($url) ? $url : '' }}" target="_blank" style="font-size: 14px;color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 10px; background:#EC595A; display: inline-block;margin: 25px 0; outline: 0;">Change Password</a>
      
      <p style="font-size: 14px;margin-bottom: 30px;">The password reset link will expire in 30 minutes.</p>
      <p style="font-size: 14px;margin-bottom: 30px;">If you don't think you created an account, please ignore this email.</p>
      <p style="font-size: 14px;margin-bottom: 20px;">- Best Regards, <span style="font-weight: 700;">Kadaku Team</span> -</p>
      <hr style="border-style: dashed;border-top: 0.9px;border-color: lightgray">
      <small style="font-size: 11px;color: gray;">
        If you're having trouble clicking the "Change Password" button, copy and paste the URL below into your web browser: <br>
        <a href="{{ isset($url) ? $url : '' }}" class="dont-break-out" style="color: #EC595A">{{ isset($url) ? $url : '' }}</a>
      </small>
    </div>
  </div>
  <footer style="margin-top:40px; margin-bottom: 45px; text-align: center; font-size: 12px; color: #999">
    <ul style="box-sizing: border-box;line-height: 1.4;font-size: 12px;padding: 0;text-align: center;margin-bottom: 10px">
      <li style="box-sizing: border-box;list-style: none;padding: 0 8px;margin: 0;display: inline-block;border-right: solid 1px #ddd">
        <a href="https://kadaku.id/blog" target="_blank" style="box-sizing: border-box;color: #999;text-decoration: none">Blog</a>
      </li>
      <li style="box-sizing: border-box;list-style: none;padding: 0 8px;margin: 0;display: inline-block;border-right: solid 1px #ddd">
        <a href="https://instagram.com/kadaku.id" target="_blank" style="box-sizing: border-box;color: #999;text-decoration: none">Instagram</a>
      </li>
      <li style="box-sizing: border-box;list-style: none;padding: 0 8px;margin: 0;display: inline-block;border-right: solid 1px #ddd">
        <a href="#" target="_blank" style="box-sizing: border-box;color: #999;text-decoration: none">Facebook</a>
      </li>
      <li style="box-sizing: border-box;list-style: none;padding: 0 8px;margin: 0;display: inline-block;">
        <a href="#" target="_blank" style="box-sizing: border-box;color: #999;text-decoration: none">Twitter</a>
      </li>
    </ul>
    <p class="">© {{ date('Y') }} - <a href="https://kadaku.id" style="color: #EC595A;text-decoration: none">kadaku.id</a>. All rights reserved. Indonesia</p>
  </footer>
</body>
</html>