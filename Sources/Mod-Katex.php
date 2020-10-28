<?php

/**
 * @package Katex MOD
 * @version 1.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 */

if (!defined('SMF'))
	die('No direct access...');

class Katex
{
	public static function load_customs()
	{
		loadCSSFile('https://cdn.jsdelivr.net/npm/katex@0.12.0/dist/katex.min.css', array('external' => true, 'minimize' => false, 'attributes' => ['integrity' => 'sha384-AfEj0r4/OFrOo5t7NnNe46zW/tFgW6x/bCJG8FqQCEo3+Aro6EYUG4+cU+KJWu/X', 'crossorigin' => 'anonymous']));
		loadJavaScriptFile('https://cdn.jsdelivr.net/npm/katex@0.12.0/dist/katex.min.js', array('external' => true, 'minimize' => false, 'attributes' => ['integrity' => 'sha384-g7c+Jr9ZivxKLnZTDUhnkOnsh30B4H0rpLUpJ4jAIKs4fnJI+sEnkvrMWph2EDg4', 'crossorigin' => 'anonymous'], 'defer' => true));
		loadJavaScriptFile('https://cdn.jsdelivr.net/npm/katex@0.12.0/dist/contrib/auto-render.min.js', array('external' => true, 'minimize' => false, 'attributes' => ['integrity' => 'sha384-mll67QQFJfxn0IYznZYonOWZ644AWYC+Pt2cHqMaRhXVrursRwvLnLaebdGIlYNa', 'crossorigin' => 'anonymous', 'onload' => 'renderMathInElement(document.body);'], 'defer' => true));
	}

	public static function bbc_buttons(&$bbc_tags, &$editor_tag_map)
	{
		$bbc_tags[count($bbc_tags)-1][] = [];
		$bbc_tags[count($bbc_tags)-1][] = [
			'image' => 'katex_inline',
			'code' => 'katex_inline',
			'before' => '[katex]',
			'after' => '[/katex]',
			'description' => 'KaTeX'
		];
		$bbc_tags[count($bbc_tags)-1][] = [
			'image' => 'katex',
			'code' => 'katex',
			'before' => '[katex]',
			'after' => '[/katex]',
			'description' => 'KaTeX'
		];
	}

	public static function bbc_code(&$codes)
	{
		$codes[] = [
			'tag' => 'katex',
			'type' => 'unparsed_content',
			'content' => '\[ $1 \]',
		];
	
		$codes[] = [
			'tag' => 'katex',
			'type' => 'unparsed_equals_content',
			'content' => '\( $1 \)',
		];
	}

	public static function preview()
	{
		addInlineJavaScript('
			smc_preview_post.prototype.onDocSent = function (XMLDoc)
			{
				if (!XMLDoc)
				{
					document.forms.postmodify.preview.onclick = new function ()
					{
						return true;
					}
					document.forms.postmodify.preview.click();
				}
			
				// Show the preview section.
				var preview = XMLDoc.getElementsByTagName(\'smf\')[0].getElementsByTagName(\'preview\')[0];
				setInnerHTML(document.getElementById(this.opts.sPreviewSubjectContainerID), preview.getElementsByTagName(\'subject\')[0].firstChild.nodeValue);
			
				var bodyText = \'\';
				for (var i = 0, n = preview.getElementsByTagName(\'body\')[0].childNodes.length; i < n; i++)
					if (preview.getElementsByTagName(\'body\')[0].childNodes[i].nodeValue != null)
						bodyText += preview.getElementsByTagName(\'body\')[0].childNodes[i].nodeValue;
			
				setInnerHTML(document.getElementById(this.opts.sPreviewBodyContainerID), bodyText);
				document.getElementById(this.opts.sPreviewBodyContainerID).className = \'windowbg\';
			
				// Show a list of errors (if any).
				var errors = XMLDoc.getElementsByTagName(\'smf\')[0].getElementsByTagName(\'errors\')[0];
				var errorList = new Array();
				for (var i = 0, numErrors = errors.getElementsByTagName(\'error\').length; i < numErrors; i++)
					errorList[errorList.length] = errors.getElementsByTagName(\'error\')[i].firstChild.nodeValue;
				document.getElementById(this.opts.sErrorsContainerID).style.display = numErrors == 0 ? \'none\' : \'\';
				document.getElementById(this.opts.sErrorsContainerID).className = errors.getAttribute(\'serious\') == 1 ? \'errorbox\' : \'noticebox\';
				document.getElementById(this.opts.sErrorsSeriousContainerID).style.display = numErrors == 0 ? \'none\' : \'\';
				setInnerHTML(document.getElementById(this.opts.sErrorsListContainerID), numErrors == 0 ? \'\' : errorList.join(\'<br>\'));
			
				// Adjust the color of captions if the given data is erroneous.
				var captions = errors.getElementsByTagName(\'caption\');
				for (var i = 0, numCaptions = errors.getElementsByTagName(\'caption\').length; i < numCaptions; i++)
				{
					if (document.getElementById(this.opts.sCaptionContainerID.replace(\'%ID%\', captions[i].getAttribute(\'name\'))))
						document.getElementById(this.opts.sCaptionContainerID.replace(\'%ID%\', captions[i].getAttribute(\'name\'))).className = captions[i].getAttribute(\'class\');
				}
			
				if (errors.getElementsByTagName(\'post_error\').length == 1)
					document.forms.postmodify[this.opts.sPostBoxContainerID].style.border = \'1px solid red\';
				else if (document.forms.postmodify[this.opts.sPostBoxContainerID].style.borderColor == \'red\' || document.forms.postmodify[this.opts.sPostBoxContainerID].style.borderColor == \'red red red red\')
				{
					if (\'runtimeStyle\' in document.forms.postmodify[this.opts.sPostBoxContainerID])
						document.forms.postmodify[this.opts.sPostBoxContainerID].style.borderColor = \'\';
					else
						document.forms.postmodify[this.opts.sPostBoxContainerID].style.border = null;
				}
			
				// Set the new last message id.
				if (\'last_msg\' in document.forms.postmodify)
					document.forms.postmodify.last_msg.value = XMLDoc.getElementsByTagName(\'smf\')[0].getElementsByTagName(\'last_msg\')[0].firstChild.nodeValue;
			
				var ignored_replies = new Array(), ignoring;
				var newPosts = XMLDoc.getElementsByTagName(\'smf\')[0].getElementsByTagName(\'new_posts\')[0] ? XMLDoc.getElementsByTagName(\'smf\')[0].getElementsByTagName(\'new_posts\')[0].getElementsByTagName(\'post\') : {length: 0};
				var numNewPosts = newPosts.length;
				if (numNewPosts != 0)
				{
					var newPostsHTML = \'<span id="new_replies"><\' + \'/span>\';
					var tempHTML;
					var new_replies = new Array();
					for (var i = 0; i < numNewPosts; i++)
					{
						new_replies[i] = newPosts[i].getAttribute("id");
			
						ignoring = false;
						if (newPosts[i].getElementsByTagName("is_ignored")[0].firstChild.nodeValue != 0)
							ignored_replies[ignored_replies.length] = ignoring = newPosts[i].getAttribute("id");
			
						tempHTML = this.opts.newPostsTemplate.replaceAll(\'%PostID%\', newPosts[i].getAttribute("id")).replaceAll(\'%PosterName%\', newPosts[i].getElementsByTagName("poster")[0].firstChild.nodeValue).replaceAll(\'%PostTime%\', newPosts[i].getElementsByTagName("time")[0].firstChild.nodeValue).replaceAll(\'%PostBody%\', newPosts[i].getElementsByTagName("message")[0].firstChild.nodeValue).replaceAll(\'%IgnoredStyle%\', ignoring ?  \'display: none\' : \'\');
			
						newPostsHTML += tempHTML;
					}
			
					// Remove the new image from old-new replies!
					for (i = 0; i < new_replies.length; i++)
						document.getElementById(this.opts.sNewImageContainerID.replace(\'%ID%\', new_replies[i])).style.display = \'none\';
			
					setOuterHTML(document.getElementById(\'new_replies\'), newPostsHTML);
				}
			
				var numIgnoredReplies = ignored_replies.length;
				if (numIgnoredReplies != 0)
				{
					for (var i = 0; i < numIgnoredReplies; i++)
					{
						aIgnoreToggles[ignored_replies[i]] = new smc_Toggle({
							bToggleEnabled: true,
							bCurrentlyCollapsed: true,
							aSwappableContainers: [
								\'msg_\' + ignored_replies[i] + \'_body\',
								\'msg_\' + ignored_replies[i] + \'_quote\',
							],
							aSwapLinks: [
								{
									sId: \'msg_\' + ignored_replies[i] + \'_ignored_link\',
									msgExpanded: \'\',
									msgCollapsed: this.opts.sTxtIgnoreUserPost
								}
							]
						});
					}
				}
			
				location.hash = \'#\' + this.opts.sPreviewSectionContainerID;
			
				if (typeof(smf_codeFix) != \'undefined\')
					smf_codeFix();
			
				// Render KaTeX for this preview
				renderMathInElement(document.getElementById(\'preview_body\'));
			}',
		true);
	}

	public static function quick_edit(&$deprecated)
	{
		addInlineJavaScript('
		QuickModify.prototype.onModifyDone = function (XMLDoc)
		{
			// We\'ve finished the loading stuff.
			ajax_indicator(false);
		
			// If we didn\'t get a valid document, just cancel.
			if (!XMLDoc || !XMLDoc.getElementsByTagName(\'smf\')[0])
			{
				// Mozilla will nicely tell us what\'s wrong.
				if (XMLDoc.childNodes.length > 0 && XMLDoc.firstChild.nodeName == \'parsererror\')
					setInnerHTML(document.getElementById(\'error_box\'), XMLDoc.firstChild.textContent);
				else
					this.modifyCancel();
		
				return;
			}
		
			var message = XMLDoc.getElementsByTagName(\'smf\')[0].getElementsByTagName(\'message\')[0];
			var body = message.getElementsByTagName(\'body\')[0];
			var error = message.getElementsByTagName(\'error\')[0];
		
			if (body)
			{
				// Show new body.
				var bodyText = \'\';
				for (var i = 0; i < body.childNodes.length; i++)
					bodyText += body.childNodes[i].nodeValue;
		
				this.sMessageBuffer = this.opt.sTemplateBodyNormal.replace(/%body%/, bodyText.replace(/\$/g, \'{&dollarfix;$}\')).replace(/\{&dollarfix;\$\}/g,\'$\');
				setInnerHTML(this.oCurMessageDiv, this.sMessageBuffer);
		
				// Show new subject, but only if we want to...
				var oSubject = message.getElementsByTagName(\'subject\')[0];
				var sSubjectText = oSubject.childNodes[0].nodeValue.replace(/\$/g, \'{&dollarfix;$}\');
				var sTopSubjectText = oSubject.childNodes[0].nodeValue.replace(/\$/g, \'{&dollarfix;$}\');
				this.sSubjectBuffer = this.opt.sTemplateSubjectNormal.replace(/%msg_id%/g, this.sCurMessageId.substr(4)).replace(/%subject%/, sSubjectText).replace(/\{&dollarfix;\$\}/g,\'$\');
				setInnerHTML(this.oCurSubjectDiv, this.sSubjectBuffer);
		
				// If this is the first message, also update the topic subject.
				if (oSubject.getAttribute(\'is_first\') == \'1\')
					setInnerHTML(document.getElementById(\'top_subject\'), this.opt.sTemplateTopSubject.replace(/%subject%/, sTopSubjectText).replace(/\{&dollarfix;\$\}/g, \'$\'));
		
				// Render KaTeX for this edited message
				renderMathInElement(document.getElementById(\'msg_\' + this.sCurMessageId.substr(4)));
		
				// Show this message as \'modified on x by y\'.
				if (this.opt.bShowModify)
					$(\'#modified_\' + this.sCurMessageId.substr(4)).html(message.getElementsByTagName(\'modified\')[0].childNodes[0].nodeValue.replace(/\$/g, \'{&dollarfix;$}\'));
		
				// Show a message indicating the edit was successfully done.
				$(\'<div/>\',{
					text: message.getElementsByTagName(\'success\')[0].childNodes[0].nodeValue.replace(/\$/g, \'{&dollarfix;$}\'),
					class: \'infobox\'
				}).prependTo(\'#\' + this.sCurMessageId).delay(5000).fadeOutAndRemove(400);
			}
			else if (error)
			{
				setInnerHTML(document.getElementById(\'error_box\'), error.childNodes[0].nodeValue);
				document.forms.quickModForm.message.style.border = error.getAttribute(\'in_body\') == \'1\' ? this.opt.sErrorBorderStyle : \'\';
				document.forms.quickModForm.subject.style.border = error.getAttribute(\'in_subject\') == \'1\' ? this.opt.sErrorBorderStyle : \'\';
			}
		}',
		true);
	}
}