/*** Translation ***/
LanguageDirectory="zh";

function getTxt(s)
  {
  switch(s)
    {
    case "Save":return "\u4fdd\u5b58 ";
    case "Preview":return "\u9884\u89c8 ";
    case "Full Screen":return "\u5168\u5c4f\u5e55 ";
    case "Search":return "\u5bfb\u627e ";
    case "Check Spelling":return "\u62fc\u5b57\u68c0\u67e5 ";
    case "Text Formatting":return "\u5b57\u578b\u683c\u5f0f ";
    case "List Formatting":return "\u9879\u76ee\u683c\u5f0f ";
    case "Paragraph Formatting":return "\u6bb5\u843d\u683c\u5f0f ";
    case "Styles":return "\u6837\u5f0f ";
    case "Custom CSS":return "\u81ea\u8ba2  CSS";
    case "Styles & Formatting":return "\u6837\u5f0f\u53ca\u683c\u5f0f ";
    case "Style Selection":return "\u6837\u5f0f ";
    case "Paragraph":return "\u6bb5\u843d\u6807\u9898 ";
    case "Font Name":return "\u5b57\u4f53 ";
    case "Font Size":return "\u5b57\u4f53\u5927\u5c0f ";
    case "Cut":return "\u526a\u4e0b ";
    case "Copy":return "\u590d\u5236 ";
    case "Paste":return "\u7c98\u8d34 ";
    case "Undo":return "\u64a4\u9500 ";
    case "Redo":return "\u6062\u590d ";
    case "Bold":return "\u7c97\u4f53 ";
    case "Italic":return "\u659c\u4f53 ";
    case "Underline":return "\u5e95\u7ebf ";
    case "Strikethrough":return "\u5220\u9664\u7ebf ";
    case "Superscript":return "\u4e0a\u6807 ";
    case "Subscript":return "\u4e0b\u6807 ";
    case "Justify Left":return "\u9760\u5de6\u5bf9\u9f50 ";
    case "Justify Center":return "\u7f6e\u4e2d\u5bf9\u9f50 ";
    case "Justify Right":return "\u9760\u53f3\u5bf9\u9f50 ";
    case "Justify Full":return "\u5de6\u53f3\u5bf9\u9f50 ";
    case "Numbering":return "\u7f16\u53f7 ";
    case "Bullets":return "\u9879\u76ee\u7b26\u53f7 ";
    case "Indent":return "\u589e\u52a0\u7f29\u6392 ";
    case "Outdent":return "\u51cf\u5c11\u7f29\u6392 ";
    case "Left To Right":return "\u7531\u5de6\u81f3\u53f3\u586b\u5199 ";
    case "Right To Left":return "\u7531\u53f3\u81f3\u5de6\u586b\u5199 ";
    case "Foreground Color":return "\u6587\u5b57\u989c\u8272 ";
    case "Background Color":return "\u80cc\u666f\u989c\u8272 ";
    case "Hyperlink":return "\u8d85\u7ea7\u94fe\u63a5 ";
    case "Bookmark":return "\u951a\u70b9 ";
    case "Special Characters":return "\u7279\u6b8a\u5b57\u7b26 ";
    case "Image":return "\u4e0a\u4f20\u56fe\u50cf ";
    case "Flash":return "\u4e0a\u4f20\ Flash";
    case "Media":return "\u4e0a\u4f20\u89c6\u9891\u6587\u4ef6 ";
    case "Content Block":return "Content Block";
    case "Internal Link":return "\u5185\u90e8\u8fde\u7ed3 ";
    case "Internal Image":return "Internal Image";
    case "Object":return "\u5bf9\u8c61 ";
    case "Insert Table":return "\u63d2\u5165\u8868\u683c ";
    case "Table Size":return "\u8868\u683c\u64cd\u4f5c ";
    case "Edit Table":return "\u8868\u683c\u5c5e\u6027 ";
    case "Edit Cell":return "\u5355\u5143\u683c\u5c5e\u6027 ";
    case "Table":return "\u8868\u683c ";
    case "AutoTable":return "Table Auto Format";
    case "Border & Shading":return "\u8fb9\u6846\u548c\u9634\u5f71 ";
    case "Show/Hide Guidelines":return "\u663e\u793a /\u9690\u85cf\u7f51\u683c\u7ebf ";
    case "Absolute":return "\u7edd\u5bf9\u503c ";
    case "Paste from Word":return "Word\u6587\u4ef6\u53bb\u5783\u573e\u7c98\u8d34 ";
    case "Line":return "\u7ebf\u6761 ";
    case "Form Editor":return "\u7a97\u4f53\u7f16\u8f91 ";
    case "Form":return "\u7a97\u4f53 ";
    case "Text Field":return "\u6587\u5b57\u5b57\u6bb5 ";
    case "List":return "\u6e05\u5355 ";
    case "Checkbox":return "\u590d\u9009\u6846 ";
    case "Radio Button":return "\u9009\u9879\u6309\u94ae ";
    case "Hidden Field":return "\u9690\u85cf\u5b57\u6bb5 ";
    case "File Field":return "\u6863\u6848\u5b57\u6bb5 ";
    case "Button":return "\u6309\u94ae ";
    case "Clean":return "\u6e05\u9664 ";
    case "View/Edit Source":return "\u89c2\u770b /\u4fee\u6539 HTML\u6e90\u7801 ";
    case "Tag Selector":return "\u6807\u97f1\u9009\u7528 ";
    case "Clear All":return "\u5168\u90e8\u6e05\u9664 ";
    case "Tags":return "\u6807\u97f1 ";

    case "Heading 1":return "\u6807\u9898  1";
    case "Heading 2":return "\u6807\u9898  2";
    case "Heading 3":return "\u6807\u9898  3";
    case "Heading 4":return "\u6807\u9898  4";
    case "Heading 5":return "\u6807\u9898  5";
    case "Heading 6":return "\u6807\u9898  6";
    case "Preformatted":return "\u9884\u5148\u683c\u5f0f\u5316 ";
    case "Normal (P)":return "\u6bb5\u843d  (P)";
    case "Normal (DIV)":return "\u6bb5\u843d  (DIV)";

    case "Size 1":return "\u5b57\u578b  1";
    case "Size 2":return "\u5b57\u578b  2";
    case "Size 3":return "\u5b57\u578b  3";
    case "Size 4":return "\u5b57\u578b  4";
    case "Size 5":return "\u5b57\u578b  5";
    case "Size 6":return "\u5b57\u578b  6";
    case "Size 7":return "\u5b57\u578b  7";

    case "Are you sure you wish to delete all contents?":
      return "\u60a8\u786e\u5b9a\u8981\u5220\u9664\u6240\u6709\u5185\u5bb9\u5417\uff1f ";
    case "Remove Tag":return "\u5220\u9664\u6807\u7b7e ";
    case "Custom Colors":return "\u81ea\u8ba2\u8272\u5f69 ";
    case "More Colors...":return "\u66f4\u591a\u8272\u5f69 ...";
    case "Box Formatting":return "\u50a8\u5b58\u683c\u683c\u5f0f\u5316 ";
    case "Advanced Table Insert":return "\u63d2\u5165\u8868\u683c\u9009\u5355 ";
    case "Edit Table/Cell":return "\u4fee\u6539\u8868\u683c /\u50a8\u5b58\u683c ";
    case "Print":return "\u6253\u5370 ";
    case "Paste Text":return "\u7c98\u8d34\u4e3a\u6587\u672c ";
    case "CSS Builder":return "CSS\u521b\u5efa\u5668 ";
    case "Remove Formatting":return "\u53bb\u9664\u683c\u5f0f ";
    case "Table Dimension Text": return "Table";
    case "Table Advance Link": return "Advanced";

    case "Fonts": return "Fonts";
    case "Text": return "Text";
    case "Link": return "Link";
    case "YoutubeVideo": return "Youtube Video";
    case "Search & Replace": return "Search & Replace";
    case "HTML Editor": return "HTML Editor";
    case "Emoticons": return "Emoticons";
    case "PasteWarning": return "Please paste using the keyborad (CTRL-V)."; /*Your browser security settings don't permit this operation.*/
    case "Quote": return "Quote";
    default:return "";
    }
  }
