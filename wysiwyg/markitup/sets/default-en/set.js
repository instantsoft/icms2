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
		{name:'Quote', openWith:'<blockquote>[![Quote]!]', closeWith:'</blockquote>', className: 'btnQuote'},
        {name:'Link', key:'L', openWith:'<a target="_blank" href="[![Link URL:!:http://]!]">', closeWith:'</a>', placeHolder:'Link title...', className: 'btnLink'},
		{name:'Image from URL', replaceWith:'<img src="[![Image URL:!:http://]!]" alt="[![Image description]!]" />', className: 'btnImg'},
		{name:'Upload Image', className: 'btnImgUpload', beforeInsert: function(markItUp) { InlineUpload.display(markItUp) }},
		{name:'YouTube Video', openWith:'<youtube>[![YouTube Video URL]!]', closeWith:'</youtube>', className: 'btnVideoYoutube'},
        {name:'Facebook Video', openWith:'<facebook>[![Facebook Video URL]!]', closeWith:'</facebook>', className: 'btnVideoFacebook'},
        {name:'Code', openWith:'<code type="[![Language:!:php]!]">', placeHolder:'\n\n', openWith:'<code>', closeWith:'</code>', className: 'btnCode'},
        {name:'Spoiler', openWith:'<spoiler title="[![Spoiler title:!:Spoiler]!]">', placeHolder:'\n\n', closeWith:'</spoiler>', className: 'btnSpoiler'},
        {name:'Smiles', className: 'btnSmiles', key: 'Z', beforeInsert: function(markItUp) { insertSmiles.displayPanel(markItUp); }}
	]
};