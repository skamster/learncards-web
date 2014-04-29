

<style type="text/css">    
    /* -------------------------------------------------------------------
// markItUp!
// By Jay Salvat - http://markitup.jaysalvat.com/
// ------------------------------------------------------------------*/
.markItUp .markItUpButton1 a {
	background-image:url({$folder}markitup/sets/markdown/images/h1.png); 
}
.markItUp .markItUpButton2 a {
	background-image:url({$folder}markitup/sets/markdown/images/h2.png); 
}
.markItUp .markItUpButton3 a {
	background-image:url({$folder}markitup/sets/markdown/images/h3.png); 
}
.markItUp .markItUpButton4 a {
	background-image:url({$folder}markitup/sets/markdown/images/h4.png); 
}
.markItUp .markItUpButton5 a {
	background-image:url({$folder}markitup/sets/markdown/images/h5.png); 
}
.markItUp .markItUpButton6 a {
	background-image:url({$folder}markitup/sets/markdown/images/h6.png); 
}

.markItUp .markItUpButton7 a {
	background-image:url({$folder}markitup/sets/markdown/images/bold.png);
}
.markItUp .markItUpButton8 a {
	background-image:url({$folder}markitup/sets/markdown/images/italic.png);
}

.markItUp .markItUpButton9 a {
	background-image:url({$folder}markitup/sets/markdown/images/list-bullet.png);
}
.markItUp .markItUpButton10 a {
	background-image:url({$folder}markitup/sets/markdown/images/list-numeric.png);
}

.markItUp .markItUpButton11 a {
	background-image:url({$folder}markitup/sets/markdown/images/picture.png); 
}
.markItUp .markItUpButton12 a {
	background-image:url({$folder}markitup/sets/markdown/images/link.png);
}

.markItUp .markItUpButton13 a	{
	background-image:url({$folder}markitup/sets/markdown/images/quotes.png);
}
.markItUp .markItUpButton14 a	{
	background-image:url({$folder}markitup/sets/markdown/images/code.png);
}

.markItUp .preview a {
	background-image:url({$folder}markitup/sets/markdown/images/preview.png);
}
</style>


<form id="createSiteForm" action="plugin.php?plugin={$pluginId}" method="POST">
    <table style="width:90%">
        <tr>
            <td>Sitename</td><td>
            <input name="createSiteName" type="text" placeholder="Enter sitename" autofocus>
            </td>
        </tr>
        <tr>
            <td>Text</td>
            <td>
            <textarea id="wiki" cols="80" rows="20">fooooo</textarea></td>
        </tr>
        <tr>
            <td colspan="2"><button id="submit"></button>
            <script>
                require(["dijit/form/Button", "dojo/domReady!"], function(Button) {
                    var button = new Button({
                        iconClass : "dijitEditorIcon dijitEditorIconSave",
                        label : "Create site!",
                        onClick : function() {
                            document.forms["createSiteForm"].submit();
                        }
                    }, "submit");
                    button.startup();
                });
            </script></td>
        </tr>
    </table>
</form>
<script type="text/javascript">
$(function() {
    {literal}
    myMarkdownSettings = {
    nameSpace:          'markdown', // Useful to prevent multi-instances CSS conflict
    previewParserPath:  {/literal}"{$folder}ajaxHandler.php",{literal}
    onShiftEnter:       {keepDefault:false, openWith:'\n\n'},
    markupSet: [
        {name:'First Level Heading', key:"1", placeHolder:'Your title here...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '=') } },
        {name:'Second Level Heading', key:"2", placeHolder:'Your title here...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '-') } },
        {name:'Heading 3', key:"3", openWith:'### ', placeHolder:'Your title here...' },
        {name:'Heading 4', key:"4", openWith:'#### ', placeHolder:'Your title here...' },
        {name:'Heading 5', key:"5", openWith:'##### ', placeHolder:'Your title here...' },
        {name:'Heading 6', key:"6", openWith:'###### ', placeHolder:'Your title here...' },
        {separator:'---------------' },        
        {name:'Bold', key:"B", openWith:'**', closeWith:'**'},
        {name:'Italic', key:"I", openWith:'_', closeWith:'_'},
        {separator:'---------------' },
        {name:'Bulleted List', openWith:'- ' },
        {name:'Numeric List', openWith:function(markItUp) {
            return markItUp.line+'. ';
        }},
        {separator:'---------------' },
        {name:'Picture', key:"P", replaceWith:'![[![Alternative text]!]]([![Url:!:http://]!] "[![Title]!]")'},
        {name:'Link', key:"L", openWith:'[', closeWith:']([![Url:!:http://]!] "[![Title]!]")', placeHolder:'Your text to link here...' },
        {separator:'---------------'},    
        {name:'Quotes', openWith:'> '},
        {name:'Code Block / Code', openWith:'(!(\t|!|`)!)', closeWith:'(!(`)!)'},
        {separator:'---------------'},
        {name:'Preview', call:'preview', className:"preview"}
    ]
}

// mIu nameSpace to avoid conflict.
miu = {
    markdownTitle: function(markItUp, char) {
        heading = '';
        n = $.trim(markItUp.selection||markItUp.placeHolder).length;
        for(i = 0; i < n; i++) {
            heading += char;
        }
        return '\n'+heading+'\n';
    }
}
    {/literal}
	// Add markItUp! to your textarea in one line
	// $('textarea').markItUp( { Settings }, { OptionalExtraSettings } );
    {literal}$('#wiki').markItUp(myMarkdownSettings);{/literal}



	// You can add content from anywhere in your page
	// $.markItUp( { Settings } );	
	$('.add').click(function() {
 		$('#markItUp').markItUp('insert',
			{ 	openWith:'<opening tag>',
				closeWith:'<\/closing tag>',
				placeHolder:"New content"
			}
		);
 		return false;
	});
	
	// And you can add/remove markItUp! whenever you want
	// $(textarea).markItUpRemove();
	$('.toggle').click(function() {
		if ($("#markItUp.markItUpEditor").length === 1) {
 			$("#markItUp").markItUp('remove');
			$("span", this).text("get markItUp! back");
		} else {
			$('#markItUp').markItUp(mySettings);
			$("span", this).text("remove markItUp!");
		}
 		return false;
	});
});
</script>


{if $singleEditSite}
<script type="text/javascript">
    require(["dojo/parser", "dijit/Editor", "dijit/_editor/plugins/FontChoice", // 'fontName','fontSize','formatBlock'
    "dijit/_editor/plugins/TextColor", "dijit/_editor/plugins/LinkDialog", "dijit/_editor/plugins/AlwaysShowToolbar", "dijit/_editor/plugins/ViewSource", "dijit/_editor/plugins/FullScreen"]);
</script>
<form id="editSiteForm" action="plugin.php?plugin={$pluginId}&amp;singleEditViewId={$singleEditSite->id}" method="POST">
    <table style="width:90%">
        <tr>
            <td>Sitename</td><td>
            <input type="text" name="editSiteName" value="{$singleEditSite->name}" placeholder="Enter sitename" autofocus />
            </td>
        </tr>
        <tr>
            <td>Text</td>
            <td>
            <input type="hidden" name="editSiteContent" id='editEditorSend' />
            <div data-dojo-type="dijit/Editor" class="editor" id="editEditor" data-dojo-props="{literal}extraPlugins:['foreColor','hiliteColor',{name:'dijit/_editor/plugins/FontChoice', command:'fontName', generic:true},'createLink', 'unlink', 'insertImage','viewsource', 'findreplace','fullscreen','dijit/_editor/plugins/AlwaysShowToolbar','preview'],onChange:function(){document.getElementById('editEditorSend').value = this.getValue();}"{/literal}>
                <p>
                    {$singleEditSite->content}
                </p>
            </div></td>
        </tr>
        <tr>
            <td colspan="2"><button id="submit"></button>
            <script>
                require(["dijit/form/Button", "dojo/domReady!"], function(Button) {
                    var button = new Button({
                        iconClass : "dijitEditorIconSave",
                        label : "Send it!",
                        onClick : function() {
                            require(["dijit/registry", "dojo/dom"], function(registry, dom) {
                                var hiddenF = dom.byId("editEditorSend");
                                var dEditor = registry.byNode(dom.byId("editEditor"));
                                hiddenF.value = dEditor.get('value');
                            });
							 
                            document.forms["editSiteForm"].submit();
                           
                        }
                    }, "submit");
                    button.startup();
                });
            </script></td>
        </tr>
    </table>
</form>

{/if}

{if $singleViewSite}
<ul>
    <div class="menu">
        {foreach $siteListMenu as $site}
        <a class="button view" href="plugin.php?plugin={$pluginId}&amp;singleViewId={$site->id}">{$site->name} </a>
        {/foreach}
    </div>
</ul>
{$singleViewSite->content}
{/if}
