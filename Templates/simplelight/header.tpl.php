<!DOCTYPE HTML>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">




<!--================ DYNAMIC DRIVE SOURCE ===============--->

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>

<script type="text/javascript" src="../../Templates/simplelight/js/ddaccordion.js">
/***********************************************
* Accordion Content script- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
* Visit http://www.dynamicDrive.com for hundreds of DHTML scripts
* Please keep this notice intact
***********************************************/
</script>

<script>
ddaccordion.init({
	headerclass: "expandable", //Shared CSS class name of headers group that are expandable
	contentclass: "categoryitems", //Shared CSS class name of contents group
	revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false 
	defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc]. [] denotes no content
	onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: false, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", "openheader"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["prefix", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
})
</script>

<style type="text/css">
.arrowlistmenu{
	width: 180px; /*width of accordion menu*/
}
.arrowlistmenu .menuheader{ /*CSS class for menu headers in general (expanding or not!)*/
	font: bold 14px Arial;
	color: white;
	background: black url(../../Templates/simplelight/img/titlebar.png) repeat-x center left;
	margin-bottom: 10px; /*bottom spacing between header and rest of content*/
	text-transform: uppercase;
	padding: 4px 0 4px 10px; /*header text is indented 10px*/
	cursor: hand;
	cursor: pointer;
}
.arrowlistmenu .openheader{ /*CSS class to apply to expandable header when it's expanded*/
	background-image: url(../../Templates/simplelight/img/titlebar-active.png);
}
.arrowlistmenu ul{ /*CSS for UL of each sub menu*/
	list-style-type: none;
	margin: 0;
	padding: 0;
	margin-bottom: 8px; /*bottom spacing between each UL and rest of content*/
}
.arrowlistmenu ul li{
	padding-bottom: 2px; /*bottom spacing between menu items*/
}
.arrowlistmenu ul li a{
	color: #A70303;
	background: url(../../Templates/simplelight/img/arrowbullet.png) no-repeat center left; /*custom bullet list image*/
	display: block;
	padding: 2px 0;
	padding-left: 19px; /*link text is indented 19px*/
	text-decoration: none;
	font-weight: bold;
	border-bottom: 1px solid #dadada;
	font-size: 90%;
}
.arrowlistmenu ul li a:visited{
	color: #A70303;
}
.arrowlistmenu ul li a:hover{ /*hover state CSS*/
	color: #FF4500;
	background: url(../../Templates/simplelight/img/arrowbullet-active.png) no-repeat center left; /*custom bullet list image*/
	display: block;
	padding: 2px 0;
	padding-left: 19px; /*link text is indented 19px*/
	text-decoration: none;
	font-weight: bold;
	border-bottom: 1px solid #dadada;
	font-size: 90%;
}





.alert {
	padding: 20px;
	background-color: #f44336;
	color: white;
	opacity: 1;
	transition: opacity 0.6s;
	margin-bottom: 15px;
}

.alert.success {background-color: #4CAF50;}
.alert.info {background-color: #2196F3;}
.alert.warning {background-color: #ff9800;}

.closebtn {
	margin-left: 15px;
	color: white;
	font-weight: bold;
	float: right;
	font-size: 22px;
	line-height: 20px;
	cursor: pointer;
	transition: 0.3s;
}

.closebtn:hover {
	color: black;
}
</style>

  <title> %title% </title>
  <meta name="description" content="website description" />
  <meta name="keywords" content="website keywords, website keywords" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="../../Templates/simplelight/style/style.css" />
</head>

<body>
  <div id="main">
