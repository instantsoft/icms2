var mySettings = {
    resizeHandle: false,
	onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
	onCtrlEnter:  	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>'},
	onTab:    		{keepDefault:false, replaceWith:'    '},
	markupSet:  [
		{name:'Bold', key:'B', openWith:'<b>', closeWith:'</b>', className: 'btnBold'},
		{name:'Italic', key:'I', openWith:'<i>', closeWith:'</i>', className: 'btnItalic'},
		{name:'Underline', key:'U', openWith:'<u>', closeWith:'</u>', className: 'btnUnderline'},
		{name:'Strikethrough', key:'S', openWith:'<s>', closeWith:'</s>', className: 'btnStroke'},
		{name:'List', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ul>\n', closeBlockWith:'\n</ul>', className: 'btnOl'},
		{name:'Ordered list', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ol>\n', closeBlockWith:'\n</ol>', className: 'btnUl'},
        {name:'Link', key:'L', openWith:'<a target="_blank" href="[![Link URL:!:http://]!]">', closeWith:'</a>', placeHolder:'Link title...', className: 'btnLink'}
	]
};