var cbar_v_1_cookies = {
	pt: SITE_TITLE,
	en: SITE_TITLE
};
var cbar_v_2_cookies = {
	pt: "A",
	en: "A"
};
var cbar_acceptButton = true;
var cbar_acceptText = {
	pt: "OK",
	en: "OK"
};
var cbar_declineButton = false;
var cbar_declineText = {
	pt: "Desactivar cookies",
	en: "Disable cookies"
};
var cbar_v_2_cookiesolicyButton = true;
var cbar_v_2_cookiesolicyText = {
	pt: "Saiba mais",
	en: "See More"
};
var cbar_expireDays = 30;
var cbar_alignbottom = false;
var cbar_domain = "";
var cbar_d = new Date();
cbar_d.setTime(cbar_d.getTime() + (1000 * 24 * 60 * 60 * cbar_expireDays));
var cbar_expireDays = cbar_d.toUTCString();
var cbar_cookieName = "cookiebar";
var cbar_cookieValue = "accepted";

function appendCSS()
{
	var a = "";
	a += "#cookiebar{background:#11b38b;min-height:20px;line-height:20px;color:#eeeeee;text-align:center;padding:3px 0;font-size:12px;font-family:arial}\n";
	a += "#cookiebar.fixed{position:fixed;top:0;left:0;width:100%;z-index:9999999}\n";
	a += "#cookiebar.fixed.bottom{bottom:0;top:auto}\n";
	a += "#cookiebar p{margin:0;padding:0}\n";
	a += "#cookiebar a{color:#ffffff;display:inline-block;border-radius:3px;text-decoration:none;padding:0 6px;margin-left:8px}\n";
	a += "#cookiebar .cookiebarenable{background:#000000}\n";
	a += "#cookiebar .cookiebarenable:hover{background:#404040}\n";
	a += "#cookiebar .cookiebardisable{background:#990000}\n";
	a += "#cookiebar .cookiebardisable:hover{background:#bb0000}\n";
	a += "#cookiebar .cookiebarpolicy{background:#000000}\n";
	a += "#cookiebar .cookiebarpolicy:hover{background:#404040}\n";
	a += "#background{position:absolute;top:0;left:0;width:100%;height:100%;z-index:200000000;background-color:black;opacity:.85}\n";
	a += "#outer{position:absolute;display:table;top:0;left:0;width:100%;height:100%;z-index:200000001}\n";
	a += "#middle{vertical-align:middle;text-align:center}\n";
	a += "#inner{margin-left:auto;margin-right:auto}\n";
	a += "a.linkcookie{color:#47372a;text-decoration:none}\n";
	a += ".normal{font-size:11px;color:#666666}\n";
	a += ".FontBoldCinza{font-weight:bold;font-size:8pt;color:#666}\n";
	return a
}

function appendContent(c)
{
	if (cbar_acceptButton == true)
	{
		cbar_acceptButton = '<a href="#" onclick="return SetCookies();" class="cookiebarenable">' + cbar_acceptText[c] + "</a>"
	}
	else
	{
		cbar_acceptButton = ""
	}
	if (cbar_declineButton != false)
	{
		cbar_declineButton = '<a href="#" onclick="return DeleteCookies();" class="cookiebardisable">' + cbar_declineText[c] + "</a>"
	}
	else
	{
		cbar_declineButton = ""
	}
	if (cbar_v_2_cookiesolicyButton)
	{
		cbar_v_2_cookiesolicyButton = '<a href="#" onclick="return ShowDiv();" class="cookiebarpolicy">' + cbar_v_2_cookiesolicyText[c] + "</a>"
	}
	else
	{
		cbar_v_2_cookiesolicyButton = ""
	}
	var a = "";
	if (cbar_alignbottom == true)
	{
		a = " bottom "
	}
	var b = "";
	switch (c)
	{
	case "pt":
		b += '<div id="cookiebar" class="fixed' + a + '">Os cookies ajudam-nos a oferecer os nossos servi&ccedil;os. Ao utilizar os nossos servi&ccedil;os, concorda com a nossa utiliza&ccedil;&atilde;o de cookies.' + cbar_acceptButton + "" + cbar_declineButton + " " + cbar_v_2_cookiesolicyButton + "</div>";
		b += '<div id="background" style="display:none;"></div>';
		b += '<div id="outer" style="display:none;">';
		b += '<div id="middle">';
		b += '<div id="inner" style="width:1024px;height:500px;overflow:auto;top:150px;position:relative">';
		b += '<font class="normal"><table cellpadding="0" cellspacing="0" border="0" style="background-color:#FFFFFF; color:000; font-size:13px;"><tr><td align="left" style="padding:10px"><FONT class=FontBoldCinza>Pol&iacute;tica de cookies</FONT><FONT class=normal><BR><BR>' + cbar_v_2_cookies[c] + " " + cbar_v_1_cookies[c] + " utiliza nos seus sites cookies que permitem melhorar o desempenho e a experi&ecirc;ncia do utilizador conforme descrito abaixo.<BR></FONT><BR><FONT class=FontBoldCinza>Conhecer os cookies</FONT><FONT class=normal><BR><BR>Os cookies s&atilde;o pequenos ficheiros de texto com informa&ccedil;&atilde;o relevante que o seu dispositivo de acesso (computador, telem&oacute;vel/smartphone ou tablet) carrega, atrav&eacute;s do navegador de internet (browser), quando um site &eacute; visitado pelo utilizador.<BR><BR>A coloca&ccedil;&aacute;o de cookies n&atilde;o s&oacute; ajudar&aacute; o site a reconhecer o dispositivo do utilizador na pr&oacute;xima vez que este o visita mas tamb&eacute;m, com frequ&ecirc;ncia, ser&aacute; imprescind&iacute;vel para o funcionamento do mesmo. Os cookies usados por " + cbar_v_2_cookies[c] + " " + cbar_v_1_cookies[c] + ", em todos os seus sites, n&atilde;o recolhem informa&ccedil;&otilde;es pessoais que permitam identificar o utilizador. Guardam informa&ccedil;&otilde;es gen&eacute;ricas, designadamente a forma ou local/pa&iacute;s de acesso dos utilizadores e o modo como usam os sites, entre outros. Os cookies ret&ecirc;m apenas informa&ccedil;&aacute;o relacionada com as suas prefer&ecirc;ncias.<BR><BR>O utilizador pode, a qualquer momento e atrav&eacute;s do seu navegador de internet (browser), decidir ser notificado sobre a recep&ccedil;&atilde;o de cookies, bem como bloquear a respectiva entrada no seu sistema. Salienta-se, no entanto, que a recusa de uso de cookies no site pode resultar na impossibilidade de acesso a algumas das suas &aacute;reas e n&atilde;o permitir toda a experi&ecirc;ncia de navega&ccedil;&atilde;o.<BR></FONT><BR><FONT class=FontBoldCinza>Funcionalidade dos cookies</FONT><FONT class=normal><BR><BR>Em geral, os cookies s&atilde;o usados para melhorar a experi&ecirc;ncia de navega&ccedil;&atilde;o do utilizador, aumentando a rapidez e efici&ecirc;ncia de resposta. Eliminam a necessidade de introduzir repetidamente as mesmas informa&ccedil;&otilde;es e ajudam a determinar que as mensagens publicit&aacute;rias que o utilizador v&ecirc; s&atilde;o relevantes e se ajustam aos seus interesses, ou seja, a utilidade, interesse e o n&uacute;mero de utiliza&ccedil;&otilde;es dos sites.<BR></FONT><BR><FONT class=FontBoldCinza>Diferentes tipos de cookies</FONT><FONT class=normal><BR><BR>Cada cookie utilizado tem uma fun&ccedil;&atilde;o e uma data de validade.<BR><BR>Relativamente &agrave; fun&ccedil;&atilde;o, os cookies utilizados podem ser:</FONT><BR><BR><FONT class=normal><B>Cookies essenciais - </B>Alguns cookies s&atilde;o essenciais para aceder a &aacute;reas espec&iacute;ficas dos sites " + cbar_v_1_cookies[c] + ". Permitem a navega&ccedil;&atilde;o no site e a utiliza&ccedil;&atilde;o das suas aplica&ccedil;&otilde;es, tal como o acesso a &aacute;reas seguras do site atrav&eacute;s de login. Sem estes cookies, os servi&ccedil;os que o exijam n&atilde;o podem ser prestados.</FONT><BR><BR><FONT class=normal><B>Cookies de funcionalidade - </B>Os cookies de funcionalidade permitem relembrar as prefer&ecirc;ncias do utilizador relativamente &agrave; navega&ccedil;&atilde;o no site. Assim, n&atilde;o necessita de o reconfigurar e personalizar cada vez que o visita.</FONT><BR><BR><FONT class=normal><B>Cookies anal&iacute;ticos - </B>Estes cookies s&atilde;o utilizados para analisar a forma como os utilizadores usam o site, permitindo destacar artigos ou servi&ccedil;os que podem ser do interesse dos utilizadores, e monitorizar o desempenho do site, conhecendo quais as p&aacute;ginas mais populares, qual o m&eacute;todo de liga&ccedil;&atilde;o entre p&aacute;ginas que &eacute; mais eficaz ou para determinar a raz&atilde;o de algumas p&aacute;ginas estarem a receber mensagens de erro. Estes cookies s&atilde;o utilizados apenas para efeitos de cria&ccedil;&atilde;o e an&aacute;lise estat&iacute;stica, sem nunca recolher informa&ccedil;&atilde;o de car&aacute;cter pessoal. Assim, " + cbar_v_2_cookies[c] + " " + cbar_v_1_cookies[c] + " pode fornecer uma experi&ecirc;ncia de elevada qualidade ao personalizar a sua oferta e, rapidamente, identificar e corrigir quaisquer problemas que surjam. </FONT><BR><BR><FONT class=normal>Relativamente &agrave; data de validade, os cookies utilizados podem ser:</FONT><BR><BR><FONT class=normal><B>Cookies permanentes - </B>Ficam armazenados nos seus dispositivos de acesso (computador, telem&oacute;vel/ smartphone ou tablet), ao n&iacute;vel do navegador de internet (browser), e s&atilde;o usados sempre que o utilizador visita novamente o site. Em geral, s&atilde;o usados para direccionar a navega&ccedil;&atilde;o de acordo com os interesses do utilizador, permitindo ao " + cbar_v_1_cookies[c] + " prestar um servi&ccedil;o mais personalizado.</FONT><BR><BR><FONT class=normal><B>Cookies de sess&atilde;o - </B>S&atilde;o tempor&aacute;rios, os cookies s&atilde;o gerados e est&atilde;o dispon&iacute;veis at&eacute; encerrar a sess&atilde;o. Da pr&oacute;xima vez que o utilizador aceder ao seu navegador de internet (browser) os cookies j&aacute; n&atilde;o estar&atilde;o armazenados. A informa&ccedil;&atilde;o obtida permite gerir as sess&otilde;es, identificar problemas e fornecer uma melhor experi&ecirc;ncia de navega&ccedil;&atilde;o.</FONT><BR><BR><FONT class=FontBoldCinza>Bloquear o uso de cookies</FONT><FONT class=normal><BR><BR>O utilizador pode desactivar parte ou a totalidade dos cookies a qualquer momento. Ao desactivar os cookies o site " + cbar_v_1_cookies[c] + " pode n&atilde;o funcionar correctamente.</FONT><BR><BR><FONT class=FontBoldCinza>Cookies na abertura das newsletters/emails</FONT><FONT class=normal><BR><BR>As newsletters/emails podem, para fins estat&iacute;sticos, conter uma pequena imagem que permite saber se s&atilde;o abertas e verificar os cliques atrav&eacute;s de links ou an&uacute;ncios dentro da newsletter. O utilizador tem sempre a possibilidade de desactivar o envio da newsletter/email atrav&eacute;s da op&ccedil;&atilde;o espec&iacute;fica no rodap&eacute; da newsletter/email.</FONT><BR><BR></TD></TR></TBODY></TABLE></font>";
		b += "</div>";
		b += "</div>";
		b += "</div>";
		break;
	default:
		b += '<div id="cookiebar" class="fixed' + a + '">Cookies help us deliver our services. By using our services, you agree to our use of cookies.' + cbar_acceptButton + "" + cbar_declineButton + " " + cbar_v_2_cookiesolicyButton + "</div>";
		b += '<div id="background" style="display:none;"></div>';
		b += '<div id="outer" style="display:none;">';
		b += '<div id="middle">';
		b += '<div id="inner" style="width:1024px;height:500px;overflow:auto;top:150px;position:relative">';
		b += '<font class="normal"><table cellpadding="0" cellspacing="0" border="0" style="background-color:#FFFFFF; color:000; font-size:13px"><tr><td align="left" style="padding:10px"><FONT class=FontBoldCinza>Cookies policy</FONT><FONT class=normal><BR><BR>' + cbar_v_2_cookies[c] + " " + cbar_v_1_cookies[c] + " uses on their sites cookies that allow to improve performance and user experience as described below.<BR></FONT><BR><FONT class=FontBoldCinza>Get to know the cookies</FONT><FONT class=normal><BR><BR>Cookies are small text files with relevant information that your access device (computer, mobile phone/smartphone or tablet) carries through the internet (browser) browser when a website is visited by the user.<BR><BR>The placement of cookies not only helps the site to recognize the user's device the next time you visit this but often it will be essential for the operation. Cookies used by " + cbar_v_2_cookies[c] + " " + cbar_v_1_cookies[c] + ", in all its sites do not collect personal information to identify the user. Keep general information, including the shape or location/country of access of users and how they use websites, among others. Cookies retain only information related to your preferences.<BR><BR>The user can, at any time and through your internet browser, decide to be notified about receiving cookies and block their entry into their system. It is noted, however, that the refusal to use cookies on the site may result in lack of access to some of their areas and not allow the whole browsing experience.<BR></FONT><BR><FONT class=FontBoldCinza>Cookies Funcionality</FONT><FONT class=normal><BR><BR>In general, cookies are used to improve the browsing experience of the user, increasing the speed and efficiency of response. Eliminate the need to repeatedly enter the same information and help determine which advertising messages that the user sees fit and are relevant to their interests, such as, utility, interest and the number of uses of the sites.<BR></FONT><BR><FONT class=FontBoldCinza>Diferent types of cookies</FONT><FONT class=normal><BR><BR>Each cookie used has a function and an expiration date<BR><BR>Regarding function, cookies may be used:</FONT><BR><BR><FONT class=normal><B>Essencial cookies - </B>Some cookies are essencial to access " + cbar_v_1_cookies[c] + " specific site areas. Enable site navigation and the use of its applications, such as access to secure areas of the Site through login. Without these cookies, the services you require can not be provided.</FONT><BR><BR><FONT class=normal><B>Funcionality Cookies - </B>Functionality Cookies allows to remember user preferences in relation to site navigation. That means that you don't no need to reconfigure and customize each time you visit it.</FONT><BR><BR><FONT class=normal><B>Analytical cookies- </B>These cookies are used to analyze how users use the site, allowing to highlight articles or services that may be of interest to users, and monitor site performance, knowing what the most popular pages, which method of linking pages is more effective or to determine why some pages are receiving error messages. These cookies are used only for the purpose of creating and statistical analysis, never collect personal information. So " + cbar_v_2_cookies[c] + " " + cbar_v_1_cookies[c] + " can provide a high quality experience to customize your offer and quickly identify and fix any problems that arise.</FONT><BR><BR><FONT class=normal>Regarding the expiration date, the cookies may be used:</FONT><BR><BR><FONT class=normal><B>Permanent Cookies - </B>Are stored in their access devices (computer, mobile phone/smartphone or tablet) to the internet browser level, and are used whenever you visit the site again. They are generally used to direct the navigation according to user interests, allowing the " + cbar_v_1_cookies[c] + " provide a more personalized service.</FONT><BR><BR><FONT class=normal><B>Session cookies - </B>Are temporary cookies generated and only available up until end of the session. The next time that you access your internet browser these kind of cookies will no longer be stored. The information obtained allows to manage sessions, identify problems and provide a better browsing experience.</FONT><BR><BR><FONT class=FontBoldCinza>Block the use of cookies</FONT><FONT class=normal><BR><BR>The user can disable some or all of the cookies at any time. By disabling cookies " + cbar_v_1_cookies[c] + " site may not work properly.</FONT><BR><BR><FONT class=FontBoldCinza>Cookies at the opening of newsletters/emails</FONT><FONT class=normal><BR><BR>Newsletters / emails may, for statistical purposes, contain a small image that lets you know if they are open and check the clicks through links or ads within the newsletter. You always have the ability to disable sending of the newsletter/email through specific option in the footer of the newsletter/email.</FONT><BR><BR></TD></TR></TBODY></TABLE></font>";
		b += "</div>";
		b += "</div>";
		b += "</div>";
		break
	}
	return b
}

function loadCbar()
{
	var h = checkCookie("cookiebar");
	if (!h)
	{
		var j = document.getElementById("cookiebarjs");
		var a = j.src.replace(/^[^\?]+\??/, "");
		var c = parseQuery(a);
		if (c.lang !== undefined)
		{
			var b = c.lang
		}
		else
		{
			var b = "pt"
		}
		var d = getInternetExplorerVersion();
		if (d > -1 && d > 8 || d == -1)
		{
			var i = document.getElementsByTagName("body")[0];
			var f = document.createElement("div");
			var g = "<style>" + appendCSS() + "</style>";
			f.innerHTML = g;
			var e = appendContent(b);
			f.innerHTML += e;
			i.appendChild(f)
		}
		else
		{
			var g = document.createElement("style");
			document.getElementsByTagName("head")[0].appendChild(g);
			g.setAttribute("type", "text/css");
			g.styleSheet.cssText = appendCSS();
			var i = document.getElementsByTagName("body")[0];
			var f = document.createElement("div");
			var e = appendContent(b);
			f.innerHTML = e;
			i.appendChild(f)
		}
	}
}

function getInternetExplorerVersion()
{
	var c = -1;
	if (navigator.appName == "Microsoft Internet Explorer")
	{
		var a = navigator.userAgent;
		var b = new RegExp("MSIE ([0-9]{1,}[.0-9]{0,})");
		if (b.exec(a) != null)
		{
			c = parseFloat(RegExp.$1)
		}
	}
	return c
}

function parseQuery(d)
{
	var e = new Object();
	if (!d)
	{
		return e
	}
	var a = d.split(/[;&]/);
	for (var c = 0; c < a.length; c++)
	{
		var g = a[c].split("=");
		if (!g || g.length != 2)
		{
			continue
		}
		var b = unescape(g[0]);
		var f = unescape(g[1]);
		f = f.replace(/\+/g, " ");
		e[b] = f
	}
	return e
}
window.addEventListener ? window.addEventListener("load", loadCbar, false) : window.attachEvent && window.attachEvent("onload", loadCbar);
document.onclick = function (b)
{
	b = b || window.event;
	var a = b.target || b.srcElement;
	if (a.id == "middle" || a.id == "outer")
	{
		hide_popup()
	}
};

function hide_popup()
{
	document.getElementById("outer").style.display = "none";
	document.getElementById("background").style.display = "none";
	return false
}

function ShowDiv()
{
	document.getElementById("outer").style.display = "block";
	document.getElementById("background").style.display = "block";
	return false
}

function SetCookies()
{
	var a = checkCookie("cookiebar");
	if (!a)
	{
		setCookie(cbar_cookieName, cbar_cookieValue, cbar_expireDays, "/")
	}
	document.getElementById("cookiebar").style.visibility = "hidden";
	return false
}

function setCookie(b, e, a, g, d, f)
{
	e = escape(e);
	var c = b + "=" + e + ((a) ? "; expires=" + a : "") + ((g) ? "; path=" + g : "") + ((d) ? "; domain=" + d : "") + ((f) ? "; secure" : "");
	document.cookie = c
}

function DeleteCookies()
{
	var a = checkCookie("cookiebar");
	if (a)
	{
		document.cookie = "cookiebar=; expires=Thu, 01 Jan 1970 00:00:00 UTC; domain=; path="
	}
	return false
}

function getCookie(d)
{
	var d = d + "=";
	var a = document.cookie.split(";");
	for (var c = 0; c < a.length; c++)
	{
		var b = a[c];
		while (b.charAt(0) == " ")
		{
			b = b.substring(1)
		}
		if (b.indexOf(d) != -1)
		{
			return b.substring(d.length, b.length)
		}
	}
	return ""
}

function checkCookie()
{
	var a = getCookie("cookiebar");
	if (a != "")
	{
		return true
	}
	else
	{
		return false
	}
};