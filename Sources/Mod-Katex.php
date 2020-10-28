<?php

/**
 * @package Katex MOD
 * @version 1.0
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2020, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

if (!defined('SMF'))
	die('No direct access...');

class Katex
{
	public static function load_customs()
	{
		loadCSSFile('https://cdn.jsdelivr.net/npm/katex@0.12.0/dist/katex.min.css', array('external' => true, 'minimize' => false, 'attributes' => ['integrity' => 'sha384-AfEj0r4/OFrOo5t7NnNe46zW/tFgW6x/bCJG8FqQCEo3+Aro6EYUG4+cU+KJWu/X', 'crossorigin' => 'anonymous']));
		loadJavaScriptFile('https://cdn.jsdelivr.net/npm/katex@0.12.0/dist/katex.min.js', array('external' => true, 'minimize' => false, 'attributes' => ['integrity' => 'sha384-g7c+Jr9ZivxKLnZTDUhnkOnsh30B4H0rpLUpJ4jAIKs4fnJI+sEnkvrMWph2EDg4', 'crossorigin' => 'anonymous', 'defer' => '']));
		loadJavaScriptFile('https://cdn.jsdelivr.net/npm/katex@0.12.0/dist/contrib/auto-render.min.js', array('external' => true, 'minimize' => false, 'attributes' => ['integrity' => 'sha384-mll67QQFJfxn0IYznZYonOWZ644AWYC+Pt2cHqMaRhXVrursRwvLnLaebdGIlYNa', 'crossorigin' => 'anonymous', 'onload' => 'renderMathInElement(document.body);', 'defer' => '']));
	}

	public static function bbc_buttons(&$bbc_tags, &$editor_tag_map)
	{
		$bbc_tags[count($bbc_tags)-1][] = [];
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
}