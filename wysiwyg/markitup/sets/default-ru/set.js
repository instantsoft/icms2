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
		{name:'Цитата', openWith:'<blockquote>[![Текст цитаты]!]', closeWith:'</blockquote>', className: 'btnQuote'},
        {name:'Ссылка', key:'L', openWith:'<a target="_blank" href="[![Адрес ссылки:!:http://]!]">', closeWith:'</a>', placeHolder:'Заголовок ссылки...', className: 'btnLink'},
		{name:'Фото из Интернета', replaceWith:'<img src="[![Адрес фото:!:http://]!]" alt="[![Описание]!]" />', className: 'btnImg'},
		{name:'Фото с компьютера', className: 'btnImgUpload', beforeInsert: function(markItUp) { InlineUpload.display(markItUp) }},
		{name:'Видео YouTube', openWith:'<youtube>[![Ссылка на ролик YouTube]!]', closeWith:'</youtube>', className: 'btnVideoYoutube'},
		{name:'Видео Facebook', openWith:'<facebook>[![Ссылка на ролик Facebook]!]', closeWith:'</facebook>', className: 'btnVideoFacebook'},
        {name:'Код', openWith:'<code type="[![Язык:!:php]!]">', placeHolder:'\n\n', closeWith:'</code>', className: 'btnCode'},
        {name:'Спойлер', openWith:'<spoiler title="[![Название спойлера:!:Спойлер]!]">', placeHolder:'\n\n', closeWith:'</spoiler>', className: 'btnSpoiler'},
        {name:'Смайлы', className: 'btnSmiles', key: 'Z', beforeInsert: function(markItUp) { insertSmiles.displayPanel(markItUp); }}
	]
};