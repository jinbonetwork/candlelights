candlelights
============

다음 지도 API를 이용해서 지역 행사정보를 표시하는 워드프레스 테마입니다. 세월호 침몰 사고를 주제로 열리는 촛불집회 정보를 알리기 위해 제작되었고 이후로도 지역 활동을 공유하는데 활용할 계획입니다.

이 프로젝트는 다음과 같은 외부 API 및 라이브러리들을 활용하고 있습니다.

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
| [Daum Map API][link-api-daum-map]                         | Copyright (c) Daum Communications. All rights reserved.                    | http://apis.daum.net/maps/maps3.js | 동적/정적 지도         |
| [Daum Local API][link-api-daum-local]                     | Copyright (c) Daum Communications. All rights reserved.                    | http://apis.daum.net/local/geo     | 주소 검색              |
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
[link-api-daum-map]: http://apis.map.daum.net/web/
[link-api-daum-local]: http://dna.daum.net/apis/local
[link-library-phpmailer]: https://github.com/PHPMailer/PHPMailer/
[link-library-snoopy]: http://snoopy.sourceforge.net/

[link-license-mit]: http://en.wikipedia.org/wiki/MIT_License
[link-license-apache]: http://www.apache.org/licenses/
[link-license-cc-by-nc-3-0]: http://creativecommons.org/licenses/by-nc/3.0/
[link-license-glgpl-2-1]: http://www.gnu.org/licenses/lgpl-2.1.html
[link-license-gpl]: http://www.gnu.org/copyleft/gpl.html
