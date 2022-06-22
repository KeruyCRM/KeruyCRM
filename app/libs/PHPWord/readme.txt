<?php
/*
vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Style\Font.php
*/
        //custom code to add page break, using hint as text-break value
        if($style->getHint()>0)
        {
            $css['text-break'] = $style->getHint();
        }


/*
vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Element\Text.php
*/

	//custom code to add page break;
	$text_break = false;
        if(preg_match('/text-break: ([\d]+)/',$style,$matches))       
        {                   
            $text_break = ($matches[1]>0 ? $matches[1] : 1);
        }

	//add <br>
	if($text_break) $this->closingTags .= str_repeat('<br>', $text_break);

/*
change in vendor\composer\platform_check.php */
//Remove PHP_VERSION check

/*
change in file vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Element\TextBreak.php*/
//adding style="margin-top: 0; margin-bottom: 0;"
	$content = '<p style="margin-top: 0; margin-bottom: 0;">&nbsp;</p>' . PHP_EOL;
	
/* 
change in vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Part\Head.php*/	
//added border-collapse

						'table' => array(
                //'border'         => '1px solid black',
                //'border-spacing' => '0px',
                'width '         => '100%',
								'border-collapse' => 'collapse',								
            ),
            'td' => array(
                'border' => '1px solid black',
								'padding' => '3px 5px 3px 5px',							
            ),
						
/*
vendor\phpoffice\phpword\src\PhpWord\Shared\Html.php
//protected static function parseCell($node, $element, &$styles)
added spaceBefore, spaceAfter, lineHeight
*/						

if (self::shouldAddTextRun($node)) {            
		$textStyles = self::filterOutNonInheritedStyles(self::parseInlineStyle($node, $styles['paragraph']));
								
		$textStyles['spaceBefore'] = $textStyles['paragraph']['spaceBefore']??0;
		$textStyles['spaceAfter'] = $textStyles['paragraph']['spaceAfter']??0;
		$textStyles['lineHeight'] = 1.0;  

		return $cell->addTextRun($textStyles);
}


//protected static function parseStyle($attribute, $styles)
case 'margin':
		$cValue = Converter::cssToTwip($cValue);
		$styles['paragraph']['spaceBefore'] = $cValue;
		$styles['paragraph']['spaceAfter'] = $cValue;
		break;
case 'margin-top':
		// BC change: up to ver. 0.17.0 incorrectly converted to points - Converter::cssToPoint($cValue)
		$styles['paragraph']['spaceBefore'] = Converter::cssToTwip($cValue);                    
		break;
case 'margin-bottom':
		// BC change: up to ver. 0.17.0 incorrectly converted to points - Converter::cssToPoint($cValue)
		$styles['paragraph']['spaceAfter'] = Converter::cssToTwip($cValue);
		break;