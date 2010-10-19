<?php
/**
 * Helper for rendering menus from navigation containers
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ArOn_View_Helper_ExtJsMenu
    extends Zend_View_Helper_Navigation_Menu
{	
	// extJs desktop name
    protected $_desktop = 'MyDesktop';
    
    // Public methods:

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link Zend_View_Helper_Navigation_Abstract::htmlify()}.
     *
     * @param  Zend_Navigation_Page $page  page to generate HTML for
     * @return string                      HTML string for the given page
     */
    public function module(Zend_Navigation_Page $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // get attribs for element
       /* $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass()
        );*/
        if(!($id = $this->getId($page))) return false;        
        $attribs['id'] = $id;
        $attribs['url'] = $this->getUrl($page);
        $attribs['icon'] = $page->getAction(); 
         
        $attribs['title'] = $this->view->escape($label);
		return "new Ext.app.Module({ " . $this->_htmlAttribs($attribs) . "})";
    }
	
    public function launcher(Zend_Navigation_Page $page)
    {   
    	if(!($id = $this->getId($page))) return false; 	
		return "this.getModule('" . $id . "').launcher";
    }
    
    public function menuItem(Zend_Navigation_Page $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // get attribs for element
       /* $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass()
        );*/    
        $attribs['text'] = $this->view->escape($label);
        if($page->hasPages())
        	$attribs['iconCls'] = 'drop'.$this->getResourceId($page); 
        $attribs['handler'] = "function() {" . $this->handler($page) . "return false;}";
		return $this->_htmlAttribs($attribs);
    }
    
    public function getResourceId (Zend_Navigation_Page $page){
    	$resource = explode(':',$page->getResource());
    	return end($resource);
    }
    
	public function handler(Zend_Navigation_Page $page)
    {   
    	if(!($id = $this->getId($page))) return ''; 	
		return $this->_desktop . ".getModule('" . $id . "').createWindow();";
    }
    
    protected function getId(Zend_Navigation_Page $page){
    	if(!$href = $page->getHref()) return false;
    	if(!($module = $page->getModule())) return false;
		$controller = $page->getController();
		$action = $page->getAction();
        
        return $controller . '-win-' . $action;
    	
    }
    
	protected function getUrl(Zend_Navigation_Page $page){
		if(!$href = $page->getHref()) return false;
    	if(!($module = $page->getModule())) return false;
		$controller = $page->getController();
		$action = $page->getAction();
        
        return '/' . $module . '/' . $controller . '/' . $action;
    	
    }
    
    // Render methods:

    /**
     * Renders the deepest active menu within [$minDepth, $maxDeth], (called
     * from {@link renderMenu()})
     *
     * @param  Zend_Navigation_Container $container  container to render
     * @param  array                     $active     active page and depth
     * @param  string                    $ulClass    CSS class for first UL
     * @param  string                    $indent     initial indentation
     * @param  int|null                  $minDepth   minimum depth
     * @param  int|null                  $maxDepth   maximum depth
     * @return string                                rendered menu
     */
    protected function _renderDeepestMenu(Zend_Navigation_Container $container,
                                          $ulClass,
                                          $indent,
                                          $minDepth,
                                          $maxDepth)
    {
        if (!$active = $this->findActive($container, $minDepth - 1, $maxDepth)) {
            return '';
        }

        // special case if active page is one below minDepth
        if ($active['depth'] < $minDepth) {
            if (!$active['page']->hasPages()) {
                return '';
            }
        } else if (!$active['page']->hasPages()) {
            // found pages has no children; render siblings
            $active['page'] = $active['page']->getParent();
        } else if (is_int($maxDepth) && $active['depth'] +1 > $maxDepth) {
            // children are below max depth; render siblings
            $active['page'] = $active['page']->getParent();
        }

        $ulClass = $ulClass ? ' class="' . $ulClass . '"' : '';
        $html = $indent . '<ul' . $ulClass . '>' . self::EOL;

        foreach ($active['page'] as $subPage) {
            if (!$this->accept($subPage)) {
                continue;
            }
            $liClass = $subPage->isActive(true) ? ' class="active"' : '';
            $html .= $indent . '    <li' . $liClass . '>' . self::EOL;
            $html .= $indent . '        ' . $this->htmlify($subPage) . self::EOL;
            $html .= $indent . '    </li>' . self::EOL;
        }

        $html .= $indent . '</ul>';

        return $html;
    }

    /**
     * Renders a normal menu (called from {@link renderMenu()})
     *
     * @param  Zend_Navigation_Container $container   container to render
     * @param  string                    $ulClass     CSS class for first UL
     * @param  string                    $indent      initial indentation
     * @param  int|null                  $minDepth    minimum depth
     * @param  int|null                  $maxDepth    maximum depth
     * @param  bool                      $onlyActive  render only active branch?
     * @return string
     */
    protected function _renderMenu(Zend_Navigation_Container $container,
                                   $ulClass,
                                   $indent,
                                   $minDepth,
                                   $maxDepth,
                                   $onlyActive)
    {
    	    	
        $script = array();
		$script[] = "Ext.get('loader').getUpdater().on({
					'beforeupdate':function(el, obj, params){
						Ext.get('loading').show();
					},
					'update':function(el, action){
						Ext.get('loading').hide();
						if (action && action.responseText && action.responseText[0] == '{'){
							var response = eval('(' + action.responseText + ')');
							//Ext.MessageBox.alert('Ошибка', response.success);
							if (response.success == false){
								var err = '<b>Невозможно получить данные!</b><hr />Возможно на стороне сервера произошла ошибка либо Вы неавторизированы.';
								if (response.errorMessage) err = '<b>Ответ от сервера:</b><hr />' + response.errorMessage;
								else if (response.message) err = '<b>Ответ от сервера:</b><hr />' + response.message;
								Ext.MessageBox.alert('Ошибка', err);
							}
						}
					},
					'failure':function(el, action){
						Ext.get('loading').hide();
						Ext.MessageBox.alert('Ошибка', '<b>Невозможно получить данные!</b><hr />Возможно на стороне сервера произошла ошибка либо сервер недоступен.');
					}
				});
				Ext.QuickTips.init();
			}";
		
        // find deepest active
        if ($found = $this->findActive($container, $minDepth, $maxDepth)) {
            $foundPage = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
                            RecursiveIteratorIterator::SELF_FIRST);
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        // extJs modules container (getModules)
        $modules = array();
        // extJs menu configuration (getMenuConfig)
        $menuConfig = '';
        $first = true;
        $end = false;
        foreach ($iterator as $page) {
        	$menuConfigItem = '';
        	$hasChild = ($page->hasPages()) ? true : false;
        	// generate extJs module code
        	if($module = $this->module($page))
        		$modules[] = $module;
        	
            $depth = $iterator->getDepth();
            if($depth > 0) $multiConfiguration = true;            
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibilty
                continue;
            } else if ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } else if ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages() ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;                           
                        }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }
			
            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = '';
            //$myIndent = $indent . str_repeat('        ', $depth);

            if ($depth > $prevDepth) {
                // start new ul tag
                $menuConfig .= $myIndent . " [" . self::EOL;                               
                $first = true;
            } else if ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                	$ind = '';
                    //$ind = $indent . str_repeat(' ', $i);
                    $menuConfig .= $ind . " ]" . self::EOL;
                    $menuConfig .= $ind . '}' . self::EOL;
                    if($i>0)
                    	$menuConfig .= $myIndent . '}' . self::EOL;
                    
                }
                // close previous li tag
                //$menuConfig .= $myIndent . '}' . self::EOL;
            } else {
                // close previous li tag                              	
                //$menuConfig .= $myIndent . '}' . self::EOL;               
            }

            // render li tag and page
            $liClass = $isActive ? ' class="active"' : '';
            /*$menuConfig .= $myIndent . '    <li' . $liClass . '>' . self::EOL
                   . $myIndent . '        ' . $this->lan($page) . self::EOL;*/
			if (!$first) $menuConfig .= ", ";
            if($hasChild){            	
               	$menuConfig .= " {" . $this->menuItem($page) .", menu:{ items: ";
                $first = true;
            }elseif($launcher = $this->launcher($page)){
                $menuConfig .= " " . $launcher;
                $first = false;                
            }else{
                $menuConfig .= " {" . $this->menuItem($page) . "}";
                $first = false;
             }
            // store as previous depth for next iteration
            $prevDepth = $depth;            
        }
        if ($menuConfig) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth+1; $i > 0; $i--) {
            	$myIndent = '';
                //$myIndent = $indent . str_repeat('        ', $i-1);
                $menuConfig .= $myIndent . " ]" . self::EOL;
                if($i>1)            	
	                $menuConfig .= $myIndent . '} }' . self::EOL;	                
               
            }
            $menuConfig = rtrim($menuConfig, self::EOL);
        }
        
		$script[] = "getModules :function(){ return [ ". implode(", ",$modules) .	"];}";
		$script[] = "getMenuConfig :function(){	return         " . $menuConfig . "}";
        $script[] = "getStartConfig :function(){
					return {
						title:'SHARIKI.UA - Menu',
						iconCls:'user',
						toolItems:[{
							text:'На главную',
							iconCls:'site',
							scope:this,
							handler:function () {
							document.location = '/';
						}
						}, '-', {
							text:'Выход',
							iconCls:'logout',
							scope:this,
							handler:function () {
								document.location = '/client/login/logout';
							}
						}]
					};
				}";
        
        $html = $this->_desktop . " = new Ext.app.App({	init :function(){ " . implode(",  ",$script) . " });";
        return $html;
    }
    
/**
     * Converts an associative array to a string of tag attributes.
     *
     * Overloads {@link Zend_View_Helper_HtmlElement::_htmlAttribs()}.
     *
     * @param  array $attribs  an array where each key-value pair is converted
     *                         to an attribute name and value
     * @return string          an attribute string
     */
    protected function _htmlAttribs($attribs)
    {
        // filter out null values and empty string values
        foreach ($attribs as $key => $value) {
            if ($value === null || (is_string($value) && !strlen($value))) {
                unset($attribs[$key]);
            }
        }

        $xhtml = '';
        $tmp_attribs = array();
        foreach ((array) $attribs as $key => $val) {
            $key = $this->view->escape($key);

            if (('on' == substr($key, 0, 2)) || ('constraints' == $key)) {
                // Don't escape event attributes; _do_ substitute double quotes with singles
                if (!is_scalar($val)) {
                    // non-scalar data should be cast to JSON first
                    //require_once 'Zend/Json.php';
                    $val = Zend_Json::encode($val);
                }
                $val = preg_replace('/"([^"]*)":/', '$1:', $val);
            } else {
                if (is_array($val)) {
                    $val = implode(' ', $val);
                }
                $val = $this->view->escape($val);
            }

            /*if ('id' == $key) {
                $val = $this->_normalizeId($val);
            }*/
        	if ('handler' == $key) {
                $tmp_attribs[] = "$key: $val";
            }else
            if (strpos($val, '"') !== false) {
                $tmp_attribs[] = "$key: '$val'";
            } else {
                $tmp_attribs[] = "$key: \"$val\"";
            }
            
        }
        $xhtml = implode(',',$tmp_attribs);
        return $xhtml;
    }
    
}
