function GetElement(oElement, sMatchTag) {
    while (oElement != null && oElement.tagName != sMatchTag) {
        if (oElement.tagName == "BODY") return null;
        oElement = oElement.parentNode;
    }
    return oElement;
}

function I_InsertImage(sURL, sAltText, sCssClass, sCssStyle) {
    if (sURL == "") return;

    var oEditor;
    if (parent.oUtil + '' == 'undefined') oEditor = (window.opener ? window.opener : openerWin).oUtil.oEditor;
    else oEditor = parent.oUtil.oEditor;

    if (navigator.appName.indexOf('Microsoft') != -1) {
        //IE
        if (!oEditor) return;
        var editorObj = parent.oUtil.obj;
        oEditor.focus();
        editorObj.setFocus();
        var oSel = oEditor.document.selection.createRange();
        oSel.execCommand("InsertImage", false, sURL);

        var oSel = oEditor.document.selection.createRange();
        if (oSel.parentElement) oElement = oSel.parentElement();
        else oElement = oSel.item(0);
        if (oElement.tagName == "IMG") {
            oElement.alt = sAltText;
            oElement.border = "0";
            if (sCssClass != "") oElement.className = sCssClass;
            if (sCssStyle != "") oElement.style.cssText = sCssStyle;
        }
    }
    else {
        //Moz
        oEditor.document.execCommand("InsertImage", false, sURL);

        oSel = oEditor.getSelection();
        var range = oSel.getRangeAt(0);
        var oElement = range.startContainer.childNodes[range.startOffset - 1];
        oSel = oEditor.getSelection();
        range = oEditor.document.createRange();
        range.selectNodeContents(oElement);
        oSel.removeAllRanges();
        oSel.addRange(range);
        if (oElement.tagName == "IMG") {
            oElement.setAttribute("ALT", sAltText);
            oElement.setAttribute("border", "0");
            if (sCssClass != "") oElement.setAttribute("class", sCssClass);
            if (sCssStyle != "") oElement.setAttribute("style", sCssStyle);
        }
    }
    return oElement;
}

function I_CreateLink(sURL, sTitle, sTarget, sCssClass, sCssStyle) {

    if (sURL == "" || sURL == "http://") return;

    var oEditor;
    if (parent.oUtil + '' == 'undefined') oEditor = (window.opener ? window.opener : openerWin).oUtil.oEditor;
    else oEditor = parent.oUtil.oEditor;

    if (navigator.appName.indexOf('Microsoft') != -1) {
        //IE
        if (!oEditor) return;
        var editorObj = parent.oUtil.obj;
        oEditor.focus();
        editorObj.setFocus();
        var oSel = oEditor.document.selection.createRange();
        if (oSel.text == "")//If no (text) selection, then build selection using the typed URL
        {
            var oSelTmp = oSel.duplicate();
            oSel.text = sURL;
            oSel.setEndPoint("StartToStart", oSelTmp);
            oSel.select();
        }
        oSel.execCommand("CreateLink", false, sURL);

        if (oSel.parentElement) oElement = GetElement(oSel.parentElement(), "A");
        else oElement = GetElement(oSel.item(0), "A");

        oElement.title = sTitle;
        if (sTarget != "") oElement.target = sTarget;
        if (sCssClass != "") oElement.className = sCssClass;
        if (sCssStyle != "") oElement.style.cssText = sCssStyle;

        return oElement;
    }
    else {
        //Moz	        
        var oSel = oEditor.getSelection();
        var range = oSel.getRangeAt(0);

        var emptySel = false;
        if (range.toString() == "") {
            //If no (text) selection, then build selection using the typed URL
            if (range.startContainer.nodeType == Node.ELEMENT_NODE) {
                if (range.startContainer.nodeName == "IMG") {
                    range.setStartBefore(range.startContainer);
                    emptySel = false;
                }
                else
                    if (range.startContainer.childNodes[range.startOffset] != null && range.startContainer.childNodes[range.startOffset].nodeType != Node.TEXT_NODE) {
                        if (range.startContainer.childNodes[range.startOffset].nodeName == "BR") emptySel = true; else emptySel = false;
                    }
                    else {
                        emptySel = true;
                    }
            }
            else {
                emptySel = true;
            }
        }

        if (emptySel) {
            var node = oEditor.document.createTextNode(sURL);
            range.insertNode(node);
            oEditor.document.designMode = "on";

            range = oEditor.document.createRange();
            range.setStart(node, 0);
            range.setEnd(node, sURL.length);

            oSel = oEditor.getSelection();
            oSel.removeAllRanges();
            oSel.addRange(range);
        }
        oEditor.document.execCommand("CreateLink", false, sURL);

        var oSel = oEditor.getSelection();
        /*var oElement = GetElement((window.opener ? window.opener : openerWin).getSelectedElement(oSel), "A");*/
        var oElement = GetElement((window.opener ? window.opener : parent).getSelectedElement(oSel), "A");

        oElement.setAttribute("title", sTitle);
        if (sTarget != "") oElement.setAttribute("target", sTarget);
        if (sCssClass != "") oElement.setAttribute("class", sCssClass);
        if (sCssStyle != "") oElement.setAttribute("style", sCssStyle);

        return oElement;
    }
}

function I_InsertHTML(sHTML) {

    var oEditor;
    if (parent.oUtil + '' == 'undefined') oEditor = (window.opener ? window.opener : openerWin).oUtil.oEditor;
    else oEditor = parent.oUtil.oEditor;

    if (navigator.appName.indexOf('Microsoft') != -1) {
        //IE
        if (!oEditor) return;
        var editorObj = parent.oUtil.obj;
        oEditor.focus();
        editorObj.setFocus();
        var oSel = oEditor.document.selection.createRange();
        if (oSel.parentElement) { oSel.pasteHTML(sHTML); }
        else oSel.item(0).outerHTML = sHTML;
    }
    else {
        //Moz
        oSel = oEditor.getSelection();
        var range = oSel.getRangeAt(0);

        var docFrag = range.createContextualFragment(sHTML);
        range.collapse(false);
        var lastNode = docFrag.childNodes[docFrag.childNodes.length - 1];
        range.insertNode(docFrag);
        try { oEditor.document.designMode = "on"; } catch (e) { }/*Saf tdk perlu*/
        if (lastNode.nodeType == Node.TEXT_NODE) {
            range = oEditor.document.createRange();
            range.setStart(lastNode, lastNode.nodeValue.length);
            range.setEnd(lastNode, lastNode.nodeValue.length);
            oSel = oEditor.getSelection();
            oSel.removeAllRanges();
            oSel.addRange(range);

            /*Saf tdk perlu*/
            var comCon = range.commonAncestorContainer;
            if (comCon && comCon.parentNode) {
                try { comCon.parentNode.normalize(); } catch (e) { }
            }
        }

    }
}


function I_Close() {
    if (parent.isIE) {
        self.closeWin(); return false
    }
    else {
        self.close(); return false
    } 
}