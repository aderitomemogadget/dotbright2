<!DOCTYPE HTML>
<html>
  <head>
    <title>jQuery plugin for android style pattern lock | ignitersworld.com</title>
	<meta name="description" content="Pattern Lock is a light weight plugin to simulate android like pattern lock mechanism for you hybrid app or for a website. It is easy to configure and style so you can have different type of pattern lock according to your need. Is also provide some methods to use this plugin securely." />
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon"/>
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon"/>
    <link rel="canonical"
  href="http://ignitersworld.com/lab/patternLock.html">
     <!-- fb meta -->
      <meta property="og:title" content="patternLock.js" />
      <meta property="og:type" content="website" />
      <meta property="og:url" content="http://ignitersworld.com/lab/patternLock.html" />

      <meta property="og:image" content="http://ignitersworld.com/lab/assets/images/patternLock.jpg" />

      <meta property="og:description" content="An android like pattern lock interface for your hybrid app or website."/>

      <!-- twitter meta -->
      <meta name="twitter:card" content="summary_large_image">
      <meta name="twitter:site" content="http://ignitersworld.com/lab/patternLock.html">
      <meta name="twitter:creator" content="@_syadav">
      <meta name="twitter:title" content="patternLock.js">
      <meta name="twitter:description" content="patternLock.js by @_syadav: An android like pattern lock interface for your hybrid app or website.">
  	   	
   	<link href="assets/css/base.css?33"  rel="stylesheet" type="text/css" />
	<link href="assets/css/project_base.css?33"  rel="stylesheet" type="text/css" />
	
    	<link href="assets/css/patternLock.css?33"  rel="stylesheet" type="text/css" />
     
  <!-- CSS_ADSBLOCK_START -->
<link rel="stylesheet" href="http://adblockers.opera-mini.net/css_block/default/domainless-unknown.css" type="text/css" />
<link rel="stylesheet" href="http://adblockers.opera-mini.net/css_block/default/page_9_57kq4rwov9fdxbn4ml7hvlh8zr84v0muzu6n3lce0s0xvd9cbq.css" type="text/css" />
<!-- CSS_ADSBLOCK_END -->
</head>
  <body class="layout-onepage">
		<!-- fork link -->
<div class="github-fork-ribbon-wrapper right">
    <div class="github-fork-ribbon">
        <a href="https://github.com/s-yadav/patternLock" target="_blank">Fork me on GitHub</a>
    </div>
</div>
<!-- fork link end -->
<section id="sectionLanding">
    <div id="projectHeader">
        <a class="link" href="index.html">
            <img id="iwLabLogo" src="assets/images/lab_logo.svg"/>
            <span>Ignitersworld Lab</span>
        </a>
    </div>
    <div class="section-content">
        <h1 class="plugin-name">patternLock.js</h1>
        <h3 class="plugin-desc">An android like pattern lock interface for your hybrid app or website.</h3>
        <div id="navButtons">
            <a href="https://github.com/s-yadav/patternLock">
                <button class="btn download" data-fancy-btn>Download</button>
            </a>
        </div>

        <div id="shareButtons">

            <div class="star-btn">
                <a class="github-button" href="https://github.com/s-yadav/patternLock" data-style="mega" data-count-href="/s-yadav/patternLock/stargazers" data-count-api="/repos/s-yadav/patternLock#stargazers_count" data-count-aria-label="# stargazers on GitHub" aria-label="Star s-yadav/ScrollMenu on GitHub">Star</a>
            </div>

            <div class="twt-btn">
                <a href="https://twitter.com/share" class="twitter-share-button" data-via="_syadav" data-size="large" data-hashtags="patternLock">Tweet</a>
            </div>

        </div>

    </div>
<!--    <img class="arrow-down" src="assets/images/arrow_down.svg"/>-->
</section>

		<div id="bodyContent">
       		 <div class="wrapper">
    <div id="navWrap">
        <ul class="nav">
            <li id="navIntro">
                <a href="#intro">
          patternLock
        </a>
            </li>
            <li id="navDemo">
                <a href="#example">
          Examples
        </a>
            </li>
            <li id="navDoc">
                <a href="#documentation">
          Documentaion
        </a>
                <ul class="nav">
                    <li><a href="#options">Options</a>
                    </li>
                    <li><a href="#callback">Callback</a>
                    </li>
                    <li><a href="#methods">Methods</a>
                    </li>
                    <li><a href="#mapper">Mapper</a>
                    </li>
                    <li><a href="#direction">Direction</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div id="sideContent">
        <div id="intro">
            <h2 class="experiment-title">
        Pattern Lock
      </h2>
            <p>
                Pattern Lock is a light weight plugin to simulate android like pattern lock mechanism for you hybrid app or for a website. It's easy to configure and style so you can have different type of pattern lock according to your need. Is also provide some methods to use this plugin securely.
            </p>
            <strong>How to use?</strong>
            <br>Include jquery and patternLock.js and patternLock.css to your page.
            <pre class="brush: xml;">
    &lt;link href=&quot;patternLock.css&quot;  rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;
    &lt;script src=&quot;jquery.js&quot;&gt;&lt;/script&gt;
    &lt;script src=&quot;patternLock.js&quot;&gt;&lt;/script&gt;
</pre>
            Then with simple initialization you have your pattern lock.
            <pre class="brush: js;">
var lock = new PatternLock("#patternContainer");
</pre>

        </div>
        <div id="example">
            <strong>Set you pattern. This will be used to validate all other 3*3 pattern in following examples.</strong>

            <div id="createPattern" class="pattern-holder">

            </div>
            <br />
            <br />
            <strong>Minimum initialization.</strong>
            <pre class="brush: js;">
        var lock= new PatternLock('#patternHolder');
        </pre>
            <div id="patternHolder1" class="pattern-holder">
            </div>

            <br />
            <br />

            <strong>Draw line between two points only when we reach second point.</strong>
            <pre class="brush: js;">
        var lock= new PatternLock('#patternHolder',{lineOnMove:false})
        </pre>
            <div id="patternHolder2" class="pattern-holder">
            </div>

            <br />
            <br />
            <strong>Keep pattern hidden while drawing.</strong>
            <pre class="brush: js;">
        var lock= new PatternLock('#patternHolder',{patternVisible:false});
        </pre>

            <div id="patternHolder3" class="pattern-holder">
            </div>

            <br />
            <br />
            <strong>Patternlock with direction.</strong>
            <img src="assets/images/icon-arrow.png" style="display:none"/>
            <div id="patternHolder7" class="pattern-holder">
            </div>
            <strong>JS</strong>
            <pre class="brush: js;">
        var lock= new PatternLock('#patternHolder');
        </pre>
            <strong>CSS</strong>
            <pre class="brush: css;">
        #patternHolder7 .patt-wrap {
          z-index: 10;
        }
        #patternHolder7 .patt-circ.hovered {
          background-color: #cde2f2;
          border: none;
        }
        #patternHolder7 .patt-circ.hovered .patt-dots {
          display: none;
        }
        #patternHolder7 .patt-circ.dir {
          background-image: url('../images/icon-arrow.png');
          background-position: center;
          background-repeat: no-repeat;
        }
        #patternHolder7 .patt-circ.e {
          -webkit-transform: rotate(0);
          transform: rotate(0);
        }
        #patternHolder7 .patt-circ.s-e {
          -webkit-transform: rotate(45deg);
          transform: rotate(45deg);
        }
        #patternHolder7 .patt-circ.s {
          -webkit-transform: rotate(90deg);
          transform: rotate(90deg);
        }
        #patternHolder7 .patt-circ.s-w {
          -webkit-transform: rotate(135deg);
          transform: rotate(135deg);
        }
        #patternHolder7 .patt-circ.w {
          -webkit-transform: rotate(180deg);
          transform: rotate(180deg);
        }
        #patternHolder7 .patt-circ.n-w {
          -webkit-transform: rotate(225deg);
           transform: rotate(225deg);
        }
        #patternHolder7 .patt-circ.n {
          -webkit-transform: rotate(270deg);
          transform: rotate(270deg);
        }
        #patternHolder7 .patt-circ.n-e {
          -webkit-transform: rotate(315deg);
          transform: rotate(315deg);
        }
        </pre>
            <br />
            <br />

            <strong>Allow repeating over dots</strong>
            <p>Note : Allowing repeation over dots gives a more complex pattern but have different side effects, like arrow css will not be correct, forming a multiple loop and confusing patterns.</p>
            <pre class="brush: js;">
            var lock= new PatternLock('#patternHolder',{allowRepeat : true});
            </pre>

            <div id="patternHolder8" class="pattern-holder">
            </div>
            <br />
            <br />

            <strong>Updating size and spacing between circles</strong>
            <pre class="brush: js;">
        var lock= new PatternLock('#patternHolder',{radius:30,margin:20});
        </pre>

            <div id="patternHolder4" class="pattern-holder">
            </div>

            <br />
            <br />
            <strong>Create different matrix pattern</strong>
            <pre class="brush: js;">
        var lock= new PatternLock('#patternHolder',{matrix:[4,4]});
        </pre>
            <div id="patternHolder5" class="pattern-holder">
            </div>
            <br />
            <br />
            <strong>Using mapper</strong>
            <pre class="brush: js;">
        var lock=new PatternLock('#patternHolder',{
        	mapper: function(idx){
        		return (idx%9) + 1;
       		 }
        });
        </pre>
            <div id="patternHolder6" class="pattern-holder">
            </div>
            <br />
            <br />
            <strong>Using as a captcha</strong>(Require <a href="http://ignitersworld.com/lab/patternCaptcha.html">patternCaptha</a> on node server)
            <br />Match the pattern in right side.
            <br />
            <div id="patternCaptchaHolder">
                <div id="patternUI"></div>
                <div id="patternCaptcha"></div>
                <div id="actionButton">
                    <input type="button" value="Refresh" id="refreshCaptcha" />
                    <input type="button" value="Submit" id="submitCaptcha" />
                </div>
            </div>

            <div>
                <br />
                <br />
                <h4>A beautiful example by <a href="http://codepen.io/khadkamhn/">Mohan Khadka</a></h4>
                <br />
                <p data-height="586" data-theme-id="0" data-slug-hash="EVaJLy" data-default-tab="result" data-user="khadkamhn" class='codepen'>See the Pen <a href='http://codepen.io/khadkamhn/pen/EVaJLy/'>Lock Screen</a> by Mohan Khadka (<a href='http://codepen.io/khadkamhn'>@khadkamhn</a>) on <a href='http://codepen.io'>CodePen</a>.</p>
<script async src="//assets.codepen.io/assets/embed/ei.js"></script>
            </div>


        </div>
        <div id="documentation">
            <h3 id="options">Options</h3>
            Syntax :
            <br>
            <pre class="brush: js;">
        var lock = new PatternLock('#patternHolder',{
            patternVisible:false //option
        });
        </pre>
            <table width="100%">
                <tr>
                    <th>Option</th>
                    <th>Allowed Value</th>
                    <th>Default</td>
                        <th>Description</th>
                </tr>
                <tr>
                    <td>matrix</td>
                    <td>Array defining matrix example [3,3] for 3*3 matrix.</td>
                    <td>[3,3]</td>
                    <td>To define matrix of pattern.</td>
                </tr>
                <tr>
                    <td>margin</td>
                    <td>number</td>
                    <td>20</td>
                    <td>Define margin of pattern circles.</td>
                </tr>
                <tr>
                    <td>radius</td>
                    <td>number</td>
                    <td>25</td>
                    <td>Define radius of pattern circles.</td>
                </tr>
                <tr>
                    <td>patternVisible</td>
                    <td>true,false</td>
                    <td>true</td>
                    <td>Make pattern visbile while drawing.</td>
                </tr>
                <tr>
                    <td>lineOnMove</td>
                    <td>true,false</td>
                    <td>true</td>
                    <td>Make line visible while moving mouse/finger.</td>
                </tr>
                <tr>
                    <td>enableSetPattern</td>
                    <td>true,false</td>
                    <td>false</td>
                    <td>If set to true allows to set pattern dynamically.</td>
                </tr>
                <tr>
                    <td>delimiter</td>
                    <td>string</td>
                    <td>empty string and ,(if matrix is bigger than 3*3)</td>
                    <td>Delimiter between pattern.</td>
                </tr>
                <tr>
                    <td>allowRepeat</td>
                    <td>true/false</td>
                    <td>false</td>
                    <td>If true allow repeating over dots.</td>
                </tr>
            </table>
            <h3 id="callback">Callback</h3>
            <strong>onDraw:</strong>
            <br>Will be fired when a pattern draw completes passing pattern as parameter. Syntax :
            <br>
            <pre class="brush: js;">
    var lock = new PatternLock('#patternHolder',{
        onDraw:function(pattern){
            //do something with pattern
        }
    });
    </pre>


            <h3 id="methods">Methods</h3>
            <strong>option(key,[value])</strong>
            <br>To get or set an option of pattern object. Syntax :
            <pre class="brush: js;">
    //as getter
    lock.option('matrix');
    //return [3,3];

    //as setter
    lock.option('matrix',[4,4]);
    </pre>

            <strong>reset()</strong>
            <br/>reset the drawn pattern
            <pre class="brush: js;">
    lock.reset();
    </pre>

            <strong>error()</strong>
            <br/>show a wrong pattern input
            <pre class="brush: js;">
    lock.error();
    </pre>

            <strong>getPattern()</strong>
            <br/>To get pattern after draw
            <pre class="brush: js;">
    lock.getPattern();
    </pre>

            <strong>checkForPattern(pattern,successHandler,errorHandler)</strong>
            <br/>To ask pattern object to check for specific pattern after every draw. Used on demo examples to check for pattern you created.
            <br/>Note : Pattern sent to checkForPattern should be seperated with same delimiter what you have provided on delimiter option. ie if delimiter is "-" than string will be "1-2-3-5-7";
            <pre class="brush: js;">
    lock.checkForPattern('12369',function(){
        alert("You unlocked your app");
    },function(){
        alert("Pattern is not correct");
    });
    </pre>

            <strong>setPattern(pattern)</strong>
            <br/>This method sets provided pattern dynamically to patternlock if enableSetPattern option is true.
            <pre class="brush: js;">
        var lock = patternLock('#patternHolder',{enableSetPattern : true});
        lock.setPattern('12587');
    </pre>

            <strong>disable()</strong>
            <br/>This method allows to disable patternlock temporarily.
            <pre class="brush: js;">
        lock.disable();
    </pre>

            <strong>enable()</strong>
            <br/>Once patternLock is disabled, it allows again to enable it.
            <pre class="brush: js;">
        lock.enable();
    </pre>
            <h3 id="mapper">Mapper</h3>
            <p>Mapper is way to map indexes of pattern circles with some arbitrary value to make it hard to understand sequence of pattern.
                <br/>Mapper can be provided only at the time of initializing PatternLock and can be provided in two way.
            </p>
            <strong>As a object</strong>
            <br>Syntax :
            <pre class="brush: js;">
    var lock = new PatternLock('#patternHolder',{
        mapper: {1:3,2:1,3:4,4:2,5:9,6:7,7:8,8:5,9:6}
    });
    </pre>
            <strong>As a function</strong>
            <br>Syntax :
            <pre class="brush: js;">
    var lock = new PatternLock('#patternHolder',{
        mapper: function(idx){
        	return (idx%9) + 1;
        }
    });
    </pre>
            If you are using mapper, make sure to use same mapper object or function for a user throught the life of pattern.

            <h3 id="direction">Direction</h3>
            <p>Patternlock adds directional classes on circles and lines which can be styled to give direction hints. It adds following classes,
            </p>
            <ul>
                <li><strong>dir :</strong>This class gets added when a selection circle needs to show direction.</li>
                <li><strong>n :</strong>Represent north direction.</li>
                <li><strong>n-e :</strong>Represent north east direction.</li>
                <li><strong>n-w :</strong>Represent north west direction.</li>
                <li><strong>s :</strong>Represent south direction.</li>
                <li><strong>s-e :</strong>Represent south east direction.</li>
                <li><strong>s-w :</strong>Represent south west direction.</li>
                <li><strong>e :</strong>Represent east direction.</li>
                <li><strong>w :</strong>Represent west direction.</li>
            </ul>
        </div>
    </div>
</div>

        </div>
        <section class="section" id="sectionContribute">
                <h2 class="section-title">Want to support and contribute to patternLock.js?</h2>
                <div id="contribute">
                    <p class="desc">patternLock.js needs your contribution to become best of its kind. There are lot of things you can do to contribute and support plugin.</p>
                    <ul id="contributionList" class="">
                        <li>
                            <h3 class="point-head"><i class="icon-use"></i>Use patternLock.js</h3>
                            <p>
                                The first thing you can do is use patternLock.js, make your site awesome, spread the word and let me know you are using it.
                            </p>
                        </li>
                        <li>
                            <h3 class="point-head"><i class="icon-report"></i>Report issues</h3>
                            <p>
                                Report any issues which you found or enhancement ideas to <a href="https://github.com/s-yadav/patternLock/issues">https://github.com/s-yadav/patternLock/issues</a>
                            </p>
                        </li>
                        <li>
                            <h3 class="point-head"><i class="icon-fork"></i>Contribute on code</h3>
                            <p>
                                If you feel you can improve the plugin,
                                <a href="https://github.com/s-yadav/patternLock">fork the repository</a>, update the code and send me a pull request.
                            </p>
                        </li>
                        <li>
                            <h3 class="point-head"><i class="icon-share"></i>Share</h3>
                            <p>
                                Spread the word and share it to your network.
                                <div class="share-button-wraps">
                                    <ul  class="rrssb-buttons">
                                        <li class="rrssb-email">
                                            <!-- Replace subject with your message using URL Endocding: http://meyerweb.com/eric/tools/dencoder/ -->
                                            <a href="mailto:?subject=%20Check%20out%20patternLock.js%20by%20Ignitersworld%20Lab,%20An%20android%20like%20pattern%20lock%20interface%20for%20your%20hybrid%20app%20or%20website.%20http://ignitersworld.com/lab/patternLock.html%20">
                                                <span class="rrssb-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
                <path d="M20.11 26.147c-2.335 1.05-4.36 1.4-7.124 1.4C6.524 27.548.84 22.916.84 15.284.84 7.343 6.602.45 15.4.45c6.854 0 11.8 4.7 11.8 11.252 0 5.684-3.193 9.265-7.398 9.3-1.83 0-3.153-.934-3.347-2.997h-.077c-1.208 1.986-2.96 2.997-5.023 2.997-2.532 0-4.36-1.868-4.36-5.062 0-4.75 3.503-9.07 9.11-9.07 1.713 0 3.7.4 4.6.972l-1.17 7.203c-.387 2.298-.115 3.3 1 3.4 1.674 0 3.774-2.102 3.774-6.58 0-5.06-3.27-8.994-9.304-8.994C9.05 2.87 3.83 7.545 3.83 14.97c0 6.5 4.2 10.2 10 10.202 1.987 0 4.09-.43 5.647-1.245l.634 2.22zM16.647 10.1c-.31-.078-.7-.155-1.207-.155-2.572 0-4.596 2.53-4.596 5.53 0 1.5.7 2.4 1.9 2.4 1.44 0 2.96-1.83 3.31-4.088l.592-3.72z"
                />
              </svg>
            </span>
                                                <span class="rrssb-text">email</span>
                                            </a>
                                        </li>
                                        <li class="rrssb-facebook">
                                            <!--  Replace with your URL. For best results, make sure you page has the proper FB Open Graph tags in header:
                https://developers.facebook.com/docs/opengraph/howtos/maximizing-distribution-media-content/ -->
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=http://ignitersworld.com/lab/patternLock.html" class="popup">
                                                <span class="rrssb-icon">
              <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" width="29" height="29" viewBox="0 0 29 29">
                <path d="M26.4 0H2.6C1.714 0 0 1.715 0 2.6v23.8c0 .884 1.715 2.6 2.6 2.6h12.393V17.988h-3.996v-3.98h3.997v-3.062c0-3.746 2.835-5.97 6.177-5.97 1.6 0 2.444.173 2.845.226v3.792H21.18c-1.817 0-2.156.9-2.156 2.168v2.847h5.045l-.66 3.978h-4.386V29H26.4c.884 0 2.6-1.716 2.6-2.6V2.6c0-.885-1.716-2.6-2.6-2.6z"
                class="cls-2" fill-rule="evenodd" />
              </svg>
            </span>
                                                <span class="rrssb-text">facebook</span>
                                            </a>
                                        </li>
                                        <li class="rrssb-linkedin">
                                            <!-- Replace href with your meta and URL information -->
                                            <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=http://ignitersworld.com/lab/patternLock.html&amp;title=patternLock.js%20by%20Ignitersworld%20Lab&amp;summary=An%20android%20like%20pattern%20lock%20interface%20for%20your%20hybrid%20app%20or%20website." class="popup">
                                                <span class="rrssb-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
                <path d="M25.424 15.887v8.447h-4.896v-7.882c0-1.98-.71-3.33-2.48-3.33-1.354 0-2.158.91-2.514 1.802-.13.315-.162.753-.162 1.194v8.216h-4.9s.067-13.35 0-14.73h4.9v2.087c-.01.017-.023.033-.033.05h.032v-.05c.65-1.002 1.812-2.435 4.414-2.435 3.222 0 5.638 2.106 5.638 6.632zM5.348 2.5c-1.676 0-2.772 1.093-2.772 2.54 0 1.42 1.066 2.538 2.717 2.546h.032c1.71 0 2.77-1.132 2.77-2.546C8.056 3.593 7.02 2.5 5.344 2.5h.005zm-2.48 21.834h4.896V9.604H2.867v14.73z"
                />
              </svg>
            </span>
                                                <span class="rrssb-text">linkedin</span>
                                            </a>
                                        </li>
                                        <li class="rrssb-twitter">
                                            <!-- Replace href with your Meta and URL information  -->
                                            <a href="http://twitter.com/home?status=patternLock.js,%20by%20@_syadav%20(http://ignitersworld.com/lab/patternLock.html)%20:%20An%20android%20like%20pattern%20lock%20interface%20for%20your%20hybrid%20app%20or%20website." class="popup">
                                                <span class="rrssb-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
                <path d="M24.253 8.756C24.69 17.08 18.297 24.182 9.97 24.62c-3.122.162-6.22-.646-8.86-2.32 2.702.18 5.375-.648 7.507-2.32-2.072-.248-3.818-1.662-4.49-3.64.802.13 1.62.077 2.4-.154-2.482-.466-4.312-2.586-4.412-5.11.688.276 1.426.408 2.168.387-2.135-1.65-2.73-4.62-1.394-6.965C5.574 7.816 9.54 9.84 13.802 10.07c-.842-2.738.694-5.64 3.434-6.48 2.018-.624 4.212.043 5.546 1.682 1.186-.213 2.318-.662 3.33-1.317-.386 1.256-1.248 2.312-2.4 2.942 1.048-.106 2.07-.394 3.02-.85-.458 1.182-1.343 2.15-2.48 2.71z"
                />
              </svg>
            </span>
                                                <span class="rrssb-text">twitter</span>
                                            </a>
                                        </li>

                                        <li class="rrssb-googleplus">
                                            <!-- Replace href with your meta and URL information.  -->
                                            <a href="https://plus.google.com/share?url=Check%20out%20patternLock.js%20by%20Ignitersworld%20Lab,%20An%20android%20like%20pattern%20lock%20interface%20for%20your%20hybrid%20app%20or%20website.%20http://ignitersworld.com/lab/patternLock.html" class="popup">
                                                <span class="rrssb-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
                <path d="M14.703 15.854l-1.22-.948c-.37-.308-.88-.715-.88-1.46 0-.747.51-1.222.95-1.662 1.42-1.12 2.84-2.31 2.84-4.817 0-2.58-1.62-3.937-2.4-4.58h2.098l2.203-1.384h-6.67c-1.83 0-4.467.433-6.398 2.027C3.768 4.287 3.06 6.018 3.06 7.576c0 2.634 2.02 5.328 5.603 5.328.34 0 .71-.033 1.083-.068-.167.408-.336.748-.336 1.324 0 1.04.55 1.685 1.01 2.297-1.523.104-4.37.273-6.466 1.562-1.998 1.187-2.605 2.915-2.605 4.136 0 2.512 2.357 4.84 7.288 4.84 5.822 0 8.904-3.223 8.904-6.41.008-2.327-1.36-3.49-2.83-4.73h-.01zM10.27 11.95c-2.913 0-4.232-3.764-4.232-6.036 0-.884.168-1.797.744-2.51.543-.68 1.49-1.12 2.372-1.12 2.807 0 4.256 3.797 4.256 6.24 0 .613-.067 1.695-.845 2.48-.537.55-1.438.947-2.295.95v-.003zm.032 13.66c-3.62 0-5.957-1.733-5.957-4.143 0-2.408 2.165-3.223 2.91-3.492 1.422-.48 3.25-.545 3.556-.545.34 0 .52 0 .767.034 2.574 1.838 3.706 2.757 3.706 4.48-.002 2.072-1.736 3.664-4.982 3.648l.002.017zM23.254 11.89V8.52H21.57v3.37H18.2v1.714h3.367v3.4h1.684v-3.4h3.4V11.89"
                />
              </svg>
            </span>
                                                <span class="rrssb-text">google+</span>
                                            </a>
                                        </li>
                                        
<!--
                                           <li class="rrssb-pinterest">
                                             Replace href with your meta and URL information.  
                                            <a href="http://pinterest.com/pin/create/button/?url=http://ignitersworld.com/lab/patternLock.html&amp;media=https://raw.githubusercontent.com/s-yadav/patternLock/master/screens/scrollmenu.jpg&amp;description=A%20new%20interface%20to%20replace%20your%20old%20boring%20scrollbar.">
                                                <span class="rrssb-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28">
                <path d="M14.02 1.57c-7.06 0-12.784 5.723-12.784 12.785S6.96 27.14 14.02 27.14c7.062 0 12.786-5.725 12.786-12.785 0-7.06-5.724-12.785-12.785-12.785zm1.24 17.085c-1.16-.09-1.648-.666-2.558-1.22-.5 2.627-1.113 5.146-2.925 6.46-.56-3.972.822-6.952 1.462-10.117-1.094-1.84.13-5.545 2.437-4.632 2.837 1.123-2.458 6.842 1.1 7.557 3.71.744 5.226-6.44 2.924-8.775-3.324-3.374-9.677-.077-8.896 4.754.19 1.178 1.408 1.538.49 3.168-2.13-.472-2.764-2.15-2.683-4.388.132-3.662 3.292-6.227 6.46-6.582 4.008-.448 7.772 1.474 8.29 5.24.58 4.254-1.815 8.864-6.1 8.532v.003z"
                />
              </svg>
            </span>
                                                <span class="rrssb-text">pinterest</span>
                                            </a>
                                        </li>
-->
                                    </ul>
                                </div>
                            </p>
                        </li>
                        <!--
                        <li>
                            <h3 class="point-head"><i class="icon-donate"></i>Donation</h3>
                            <p>
                                Support the open source development by making a thankful donation.
                            </p>
                        </li>
                        -->
                    </ul>
                </div>
                <h2 class="section-title">Author</h2>
                <div id="developer">
                    <div>
                        <p>
                        This plugin is developed and maintained by me ( Sudhanshu Yadav <a href="http://ignitersworld.com/me">ignitersworld.com/me</a> ) . If you have any query you can contact me at <a href="mailto:sudhanshuyadav2@gmail.com">sudhanshuyadav2@gmail.com</a>. 
                        </p>
                        <p>
                            You can also follow me on twitter <a href="https://twitter.com/_syadav" class="twitter-follow-button" data-show-count="false">Follow @_syadav</a> and github <a class="github-button" href="https://github.com/s-yadav" aria-label="Follow @s-yadav on GitHub">Follow @s-yadav</a> .
               
                        </p>
                        
                    </div>
                </div>
                <div id="otherProjects">
                    Check out my other open source work
                    <ul class="list-wrapper cf">
                        
                        <li>
                            <a href="http://ignitersworld.com/lab/imageViewer.html" title="A zooming and panning plugin inspired by google photos for your web images.">Image Viewer</a>
                        </li>
                        
                        
                        <li>
                            <a href="http://ignitersworld.com/lab/scrollmenu/" title="A new interface to replace your old boring scrollbar.">Scroll Menu</a>
                        </li>
                        
                        
                        <li>
                            <a href="http://ignitersworld.com/lab/angulargrid/" title="Pinterest like responsive masonry grid system for angular.">Angular Grid</a>
                        </li>
                        
                        
                        <li>
                            <a href="http://ignitersworld.com/lab/radialIndicator.html" title="A simple and light weight circular indicator / progressbar plugin.">Radial Indicator</a>
                        </li>
                        
                        
                          
                        <li>
                            <a href="http://ignitersworld.com/lab/jsonQ.html" title="A JavaScript library to make manipulation and extraction of data from a JSON very easy and fast.">jsonQ</a>
                        </li>
                        
                        
                        
                        <li>
                            <a href="http://ignitersworld.com/lab/contextMenu.html" title="contextMenu.js is a plugin to create windows like context menu with keyboard interaction, different type of inputs ,trigger events and much more.">contextMenu.js</a>
                        </li>
                        
                        
                        
                        <li>
                            <a href="http://ignitersworld.com/lab/patternCaptcha.html" title="Android like pattern matching captcha system for your node webapps.">patternCaptcha</a>
                        </li>
                        
                        
                        <li>
                            <a href="http://ignitersworld.com/lab">
                                More....
                            </a>
                        </li>
                    </ul>
                </div>
            </section>
		<footer id="footer">
Copyright to Sudhanshu Yadav<br />
&#169; www.ignitersworld.com .2013 - 2015. All rights reserved. 
</footer>

<!-- Hidden elements -->
<div id="status-msg"></div>
<!-- Hidden elements end -->  	
        
	<script type="text/javascript"  src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script type="text/javascript"  src="assets/js/base.js?33"></script>
	<script type="text/javascript"  src="assets/js/project_base.js?33"></script>	    
    
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

    <script async defer id="github-bjs" src="https://buttons.github.io/buttons.js"></script>

        <script src="assets/js/patternLockScript.js?33"></script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-44908334-1', 'ignitersworld.com');
      ga('send', 'pageview');
    </script>
  </body>
</html>
