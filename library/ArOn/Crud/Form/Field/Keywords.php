<?php
class ArOn_Crud_Form_Field_Keywords extends ArOn_Crud_Form_Field {

	protected $_type = 'textarea';

	public function addRelevantFuntionality($content, $element, array $options) {

		$label = $element->getLabel ();
		$textarea = '<textarea id="' . $element->getName () . '" cols="' . $element->getAttrib ( 'cols' ) . '" rows="' . $element->getAttrib ( 'rows' ) . '" name="' . $element->getName () . '"/>' . $element->getValue () . '</textarea>';

		$NL = "\n";

		$content = <<<EOT
		 <tr id="tr_{$element->getName ()}">
		 	<th>
		 		<label class="optional" for="{$element->getName()}">$label</label>
		 	</th>
		 	<td class="field_input">
		 		<div style='float:left'>
		 		$textarea
		 		</div>
		 		<div style="display: table; padding-left: 10px;float:left;">
		 		
		 			<script language="JavaScript" type="text/javascript" src="/res/js/relevant.js"></script>
		 		
					<input id="skeyword" type="text" name="skeyword" value="" style="width: 120px;" onkeypress="if(SearchIntro(event,'search_relevant_button')){return false}">
					<input id="search_relevant_button" type="button" name="search" value="find relevant" onclick="searchRelevant('skeyword', '{$element->getName()}');" style="width: 70px;">	
					
					<ul id="relevantKeywordsList" style='padding: 20px;'>
					</ul>
					
					<a id="moreRelevantKeywordsShow" href="javascript:void(0);" onclick="$('#moreRelevantKeywordsList').show(); $('#moreRelevantKeywordsShow').hide(); $('#moreRelevantKeywordsHide').show();" style="display: none;">Show more keywords &gt;&gt;&gt;</a><br/> 
					<a id="moreRelevantKeywordsHide" href="javascript:void(0);" onclick="$('#moreRelevantKeywordsList').hide(); $('#moreRelevantKeywordsShow').show(); $('#moreRelevantKeywordsHide').hide();" style="display: none;"> &lt;&lt;&lt; Hide additional keywords </a>
					
					<ul id="moreRelevantKeywordsList" style="display: none; padding: 20px;">
					</ul>		 			
		 		</div>
		 	</td>
		 </tr>
EOT;

		 		return $content;
	}

	public function updateField() {

		parent::updateField ();
		$this->element->addFilter ( 'StringTrim' )->//->addValidator('Regex',false,array('/^[a-z][a-z0-9., \'-]{2,}$/i'))
		setAttribs ( array ('rows' => 4, 'cols' => 30 ) );

		$this->element->clearDecorators ();
		$this->element->addDecorators ( array (array ('Callback', array ('callback' => array ($this, 'addRelevantFuntionality' ) ) ) ) );
	}
}