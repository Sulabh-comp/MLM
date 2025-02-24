<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Welcome</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
  </head>
  <body
    style="
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #ffffff;
      font-size: 14px;
    "
  >
    <div
      style="
        max-width: 680px;
        margin: 0 auto;
        padding: 45px 30px 60px;
        background: #f4f7ff;
        /* background-image: url(https://archisketch-resources.s3.ap-northeast-2.amazonaws.com/vrstyler/1661497957196_595865/email-template-background-banner); */
        background-repeat: no-repeat;
        background-size: 800px 452px;
        background-position: top center;
        font-size: 14px;
        color: #434343;
      "
    >
      <header>
        <table style="width: 100%;">
          <tbody>
            <tr style="height: 0;">
              <td>
                <img
                  alt=""
                  {{-- src="{{ asset('assets/img/awesome-logo.png') }}" --}}
                  height="30px"
                />
              </td>
              <td style="text-align: right;">
                <span
                  style="font-size: 16px; line-height: 30px; color: #0d21da;"
                  >{{ date('d M, Y') }}</span
                >
              </td>
            </tr>
          </tbody>
        </table>
      </header>

      <main>
        <div
          style="
            margin: 0;
            margin-top: 70px;
            padding: 92px 30px 115px;
            background: #ffffff;
            border-radius: 30px;
            text-align: center;
          "
        >
          <div style="width: 100%; max-width: 489px; margin: 0 auto;">
            @yield('content')
          </div>
        </div>
{{--
        <p
          style="
            max-width: 400px;
            margin: 0 auto;
            margin-top: 90px;
            text-align: center;
            font-weight: 500;
            color: #8c8c8c;
          "
        >
          Need help? Ask at
          <a
            href="mailto:info@awesomeholidaynepal.com"
            style="color: #499fb6; text-decoration: none;"
            >info@{{ str_replace('https://', '', MAIN_SITE) }}.com</a
          >
          or visit our
          <a
            href="{{ (MAIN_SITE.'/') }}"
            target="_blank"
            style="color: #499fb6; text-decoration: none;"
            >Website</a
          >. For quick replies, message us on WhatsApp at
            <a
                href="https://wa.me/9719843413619"
                target="_blank"
                style="color: #499fb6; text-decoration: none;"
                >+971 9843413619</a
            >.
        </p> --}}
      </main>

      <footer
      style="
        width: 100%;
        max-width: 490px;
        margin: 20px auto 0;
        text-align: center;
        border-top: 1px solid #e6ebf1;
      "
    >
      {{-- <p
        style="
          margin: 0;
          margin-top: 40px;
          font-size: 16px;
          font-weight: 600;
          color: #434343;
        "
      >
        {{APP_NAME}} Pvt. Ltd.
      </p>
      <p style="margin: 0; margin-top: 8px; color: #434343;">
        {{ config('app.company.address') }}
      </p>
      <div style="margin: 0; margin-top: 16px;">
        <a href="https://www.facebook.com/" target="_blank" style="display: inline-block;">
          <img
            width="36px"
            alt="Facebook"
            src="{{ https://bhutanhappiness.com }}/booking/assets/img/facebook.png"
          />
        </a>
        <a
          href="https://www.instagram.com/awesomeholidaysnepal/"
          target="_blank"
          style="display: inline-block; margin-left: 8px;"
        >
          <img
            width="36px"
            alt="Instagram"
            src="https://awesomeholidaysnepal.com/booking/assets/img/instagram.png"
        /></a>
        <a
          href="https://www.youtube.com/@AwesomeHolidaysNepal"
          target="_blank"
          style="display: inline-block; margin-left: 8px;"
        >
          <img
            width="36px"
            alt="Youtube"
            src="https://awesomeholidaysnepal.com/booking/assets/img/youtube.png"
        /></a>
      {{-- <a
          href="https://wa.me/9779843413619"
          target="_blank"
          style="display: inline-block; margin-left: 8px;"
      >
          <img
              width="36px"
              alt="WhatsApp"
              src="https://archisketch-resources.s3.ap-northeast-2.amazonaws.com/vrstyler/1661504218208_684135/email-template-icon-whatsapp"
          />
      </a> --}}
      <a
          href="https://x.com/AHNNepal"
          target="_blank"
          style="display: inline-block; margin-left: 8px;"
      >
          <img
              width="36px"
              alt="X"
              src="https://awesomeholidaysnepal.com/booking/assets/img/x.png"
          />
      </a>
      </div>
      <p style="margin: 0; margin-top: 16px; color: #434343;">
        Copyright © {{ date('Y') }} - {{APP_NAME}}. All rights reserved.
      </p> --}}
    </footer>
  </div>
</body>
</html>
