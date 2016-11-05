var mySettings = {
    resizeHandle: false,
	onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
	onCtrlEnter:  	{keepDefault:true},
	onTab:    		{keepDefault:false, replaceWith:'    '},
	markupSet:  [
		{name:'Жирный', key:'B', openWith:'<b>', closeWith:'</b>', className: 'btnBold'},
		{name:'Наклонный', key:'I', openWith:'<i>', closeWith:'</i>', className: 'btnItalic'},
		{name:'Подчеркнутый', key:'U', openWith:'<u>', closeWith:'</u>', className: 'btnUnderline'},
		{name:'Зачеркнутый', key:'S', openWith:'<s>', closeWith:'</s>', className: 'btnStroke'},
		{name:'Список', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ul>\n', closeBlockWith:'\n</ul>', className: 'btnOl'},
		{name:'Нумерованный список', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ol>\n', closeBlockWith:'\n</ol>', className: 'btnUl'},
        {name:'Ссылка', key:'L', openWith:'<a target="_blank" href="[![Адрес ссылки:!:http://]!]">', closeWith:'</a>', placeHolder:'Заголовок ссылки...', className: 'btnLink'}
	]
};