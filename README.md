candlelights
============

다음 지도 API를 이용해서 지역 행사정보를 표시하는 [워드프레스][link-platform-wordpress] 테마입니다. 세월호 침몰 사고를 주제로 열리는 촛불집회 정보를 알리기 위해 제작되었고 이후로도 지역 활동을 공유하는데 활용할 계획입니다. 보통의 블로그 테마로는 사용할 수 없습니다.

* 지도를 탐색하면 자동으로 해당 지역의 일정을 검색해서 목록을 갱신합니다.
* 동 이름을 검색해서 원하는 위치의 지도를 표시할 수 있습니다. 
* 목록이나 마커를 클릭하면 오버레이 팝업으로 자세한 내용을 읽을 수 있습니다.
* 팝업에는 간단한 주변 지도와 이번 달 일정이 표시됩니다.
* 일정 정보에 연락처가 있으면 메일이나 문자로 문의사항을 발송할 수 있습니다.

[link-platform-wordpress]: http://wordpress.org

설치 및 설정
------------

1. 워드프레스를 설치합니다.
2. 요구사항을 갖춥니다.
3. 테마 폴더에 본 프로젝트 파일을 업로드합니다.
4. `config.php` 파일을 열고 필요한 API 키 및 API에 대한 계정 정보를 입력합니다.
5. 테마를 활성화합니다.
6. 일정 정보를 입력할 때 위치 정보를 함께 입력합니다.
7. 현재 날짜에 해당하는 일정 정보가 지도에 표시됩니다.

요구사항
--------

이 프로젝트를 활용하려면 다음과 같은 라이브러리들이 필요합니다.

| 패키지                                                    | 라이선스                                                                   | 저장 위치                          | 용도                   |
|-----------------------------------------------------------|----------------------------------------------------------------------------|------------------------------------|------------------------|
| [All-In-One Event Calendar][link-library-ai1ec]           | Copyright (c) Timely Network Inc. All rights reserved.                     | 워드프레스 플러그인                | 일정 관리              |

이 프로젝트를 활용하려면 다음과 같은 API 및 계정 정보가 필요합니다.

| API                                                      | 라이선스                                                                   | 경로                               | 용도                   |
|-----------------------------------------------------------|----------------------------------------------------------------------------|------------------------------------|------------------------|
| [Daum Map API][link-api-daum-map]                         | Copyright (c) Daum Communications. All rights reserved.                    | http://apis.daum.net/maps/maps3.js | 지도 출력              |
| [Daum Local API][link-api-daum-local]                     | Copyright (c) Daum Communications. All rights reserved.                    | http://apis.daum.net/local/geo     | 주소 검색              |
| [Gmail Account][link-api-gmail]                           | Copyright (c) Google. All rights reserved.                                 | http://smtp.gmail.com              | 메일 발송              |
| [DotnetPia SMS Service Account][link-api-sms]             | Copyright (c) DotnetPia. All rights reserved.                              | http://nesolution.com/service/sms.aspx | 문자 발송          |

[link-library-ai1ec]: http://time.ly/
[link-api-daum-map]: http://apis.map.daum.net/web/
[link-api-daum-local]: http://dna.daum.net/apis/local
[link-api-gmail]: http://gmail.com
[link-api-sms]: http://dotnetpia.co.kr

포함한 패키지 목록
------------------

이 프로젝트는 다음과 같은 라이브러리들을 포함하고 있습니다.

| 패키지                                                    | 라이선스                                                                   | 저장 위치                          | 용도                   |
|-----------------------------------------------------------|----------------------------------------------------------------------------|------------------------------------|------------------------|
| [jquery][link-library-jquery]                             | [MIT][link-license-mit]                                                    | 워드프레스 번들                    | 자바스크립트 기능 확장 |
| [bootstrap][link-library-bootstrap]                       | [MIT][link-license-mit]                                                    | contrib/bootstrap/                 | 기본 모양새와 아이콘   |
| [bootstrap-datepicker][link-library-bootstrap-datepicker] | [Apache License Version 2.0][link-license-apache]                          | contrib/bootstrap-datepicker/      | 날짜 선택기            |
| [fancybox][link-library-fancybox]                         | [Creative Commons Attribution-NonComercial 3.0][link-license-cc-by-nc-3-0] | contrib/fancybox/                  | 오버레이 팝업          |
| [foundation-icons][link-library-foundation-icons]         | [MIT][link-license-mit]                                                    | contrib/foundation-icons/          | 추가 아이콘            |
| [moment][link-library-moment]                             | [MIT][link-license-mit]                                                    | contrib/moment/                    | clndr 요구사항         |
| [underscore][link-library-underscore]                     | [MIT][link-license-mit]                                                    | contrib/underscore/                | clndr 요구사항         |
| [clndr][link-library-clndr]                               | [MIT][link-license-mit]                                                    | contrib/clndr/                     | 일정표                 |
| [PHPMailer][link-library-phpmailer]                       | [GNU Lesser General Public License 2.1][link-license-glgpl-2-1]            | contrib/PHPMailer                  | 문의사항 메일 발송     |
| [snoopy][link-library-snoopy]                             | [GNU Public License][link-license-gpl]                                     | contrib/snoopy                     | 문의사항 문자 발송     |

[link-library-jquery]: http://jquery.com
[link-library-bootstrap]: http://getbootstrap.com
[link-library-bootstrap-datepicker]: http://bootstrap-datepicker.readthedocs.org/
[link-library-fancybox]: http://fancyapps.com
[link-library-foundation-icons]: http://zurb.com/playground/foundation-icon-fonts-3/
[link-library-moment]: http://momentjs.com
[link-library-underscore]: http://underscorejs.org
[link-library-clndr]: http://kylestetz.github.io/CLNDR/
[link-library-phpmailer]: https://github.com/PHPMailer/PHPMailer/
[link-library-snoopy]: http://snoopy.sourceforge.net/

[link-license-mit]: http://en.wikipedia.org/wiki/MIT_License
[link-license-apache]: http://www.apache.org/licenses/
[link-license-cc-by-nc-3-0]: http://creativecommons.org/licenses/by-nc/3.0/
[link-license-glgpl-2-1]: http://www.gnu.org/licenses/lgpl-2.1.html
[link-license-gpl]: http://www.gnu.org/copyleft/gpl.html

알려진 문제점
-------------

* 일정 등록화면에서 `/wordpress/wp-content/plugins/all-in-one-event-calendar/cache/` 디렉토리에 쓰기 권한이 없다는 메시지가 뜹니다. 해당 플러그인의 문제로 동작에는 지장이 없습니다.

버전 히스토리
-------------

* v0.9 -- 처음 업로드, 대부분의 기능 구현.

라이선스
--------

이 프로젝트는 [Mozilla Public License 2.0][link-license-mpl-2-0]를 따릅니다.

[link-license-mpl-2-0]: https://www.mozilla.org/MPL/2.0/


candlelights
============

This is a [WordPress][link-platform-wordpress] theme that can offer regional information for events using Daum map API. Originally this theme was created for candlelight vigils for Sewol ferry tragedy, but it will be used for sharing the information for other events later. However, it is not a theme for a blog. However, it is not a theme for a blog.

* Automatically present the regional information as dragging the map.
* Move the region through searching the name of Dong (a unit of a unit of administrative district of S. Korea)
* When selecting a position through clicking an item of the list or a marker, display the detail information (the area around the position in the form of a map, and the monthly schedule of the area of the position) with the pop-up window. You can send an e-mail or a text message to the address displayed in the schedule.

[link-platform-wordpress]: http://wordpress.org

Installation and setting
------------

1. Install WordPress.
2. Satisfy the requirements below.
3. Upload the files of this theme into the folder of `themes` in the WordPress.
4. Write the related API keys and the account information related to the API's into `config.php`.
5. Activate the theme.
6. Input location information with schedule.

Requirements
--------

The libraries below must be installed.

| Package                                                    | Licence                                                                   | Location to save                          | Use                   |
|-----------------------------------------------------------|----------------------------------------------------------------------------|------------------------------------|------------------------|
| [All-In-One Event Calendar][link-library-ai1ec]           | Copyright (c) Timely Network Inc. All rights reserved.                     | `plugins` in your WordPress               | Schedule management              |

The API's below and the account information related to them are needed.

| API                                                      | Licence                                                                   | URI                               | Use                   |
|-----------------------------------------------------------|----------------------------------------------------------------------------|------------------------------------|------------------------|
| [Daum Map API][link-api-daum-map]                         | Copyright (c) Daum Communications. All rights reserved.                    | http://apis.daum.net/maps/maps3.js | Map              |
| [Daum Local API][link-api-daum-local]                     | Copyright (c) Daum Communications. All rights reserved.                    | http://apis.daum.net/local/geo     | Search addresses             |
| [Gmail Account][link-api-gmail]                           | Copyright (c) Google. All rights reserved.                                 | http://smtp.gmail.com              | Send e-mails              |
| [DotnetPia SMS Service Account][link-api-sms]             | Copyright (c) DotnetPia. All rights reserved.                              | http://nesolution.com/service/sms.aspx | Send text messages        |

[link-library-ai1ec]: http://time.ly/
[link-api-daum-map]: http://apis.map.daum.net/web/
[link-api-daum-local]: http://dna.daum.net/apis/local
[link-api-gmail]: http://gmail.com
[link-api-sms]: http://dotnetpia.co.kr

Packages included in this theme
------------------

| Package                                                    | Licence                                                                  | Location to save                         | Use                   |
|-----------------------------------------------------------|----------------------------------------------------------------------------|------------------------------------|------------------------|
| [jquery][link-library-jquery]                             | [MIT][link-license-mit]                                                    | WordPress bundle                  | Extend the functions of Javascript |
| [bootstrap][link-library-bootstrap]                       | [MIT][link-license-mit]                                                    | contrib/bootstrap/                 | Basic layout and icons   |
| [bootstrap-datepicker][link-library-bootstrap-datepicker] | [Apache License Version 2.0][link-license-apache]                          | contrib/bootstrap-datepicker/      | Date selector            |
| [fancybox][link-library-fancybox]                         | [Creative Commons Attribution-NonComercial 3.0][link-license-cc-by-nc-3-0] | contrib/fancybox/                  | Overlay pop-up         |
| [foundation-icons][link-library-foundation-icons]         | [MIT][link-license-mit]                                                    | contrib/foundation-icons/          | Additional icons          |
| [moment][link-library-moment]                             | [MIT][link-license-mit]                                                    | contrib/moment/                    | A requirement of CLNDR.js        |
| [underscore][link-library-underscore]                     | [MIT][link-license-mit]                                                    | contrib/underscore/                | A requirement of CLNDR.js         |
| [clndr][link-library-clndr]                               | [MIT][link-license-mit]                                                    | contrib/clndr/                     | Scheduler                 |
| [PHPMailer][link-library-phpmailer]                       | [GNU Lesser General Public License 2.1][link-license-glgpl-2-1]            | contrib/PHPMailer                  | Send e-mails for inquiry    |
| [snoopy][link-library-snoopy]                             | [GNU Public License][link-license-gpl]                                     | contrib/snoopy                     | Send text messages for inquiry     |

[link-library-jquery]: http://jquery.com
[link-library-bootstrap]: http://getbootstrap.com
[link-library-bootstrap-datepicker]: http://bootstrap-datepicker.readthedocs.org/
[link-library-fancybox]: http://fancyapps.com
[link-library-foundation-icons]: http://zurb.com/playground/foundation-icon-fonts-3/
[link-library-moment]: http://momentjs.com
[link-library-underscore]: http://underscorejs.org
[link-library-clndr]: http://kylestetz.github.io/CLNDR/
[link-library-phpmailer]: https://github.com/PHPMailer/PHPMailer/
[link-library-snoopy]: http://snoopy.sourceforge.net/

[link-license-mit]: http://en.wikipedia.org/wiki/MIT_License
[link-license-apache]: http://www.apache.org/licenses/
[link-license-cc-by-nc-3-0]: http://creativecommons.org/licenses/by-nc/3.0/
[link-license-glgpl-2-1]: http://www.gnu.org/licenses/lgpl-2.1.html
[link-license-gpl]: http://www.gnu.org/copyleft/gpl.html

Known problems
-------------

* An error message which informs that the directory of `/wp-content/plugins/all-in-one-event-calendar/cache/` has not 'write permission'. The trouble arises from a plug-in included in this theme. However, the function goes well. 

Version histroy
-------------

* v0.9 -- The first upload. The most of functions are realized.

Licence
--------

This follows [Mozilla Public License 2.0][link-license-mpl-2-0].
[link-license-mpl-2-0]: https://www.mozilla.org/MPL/2.0/
