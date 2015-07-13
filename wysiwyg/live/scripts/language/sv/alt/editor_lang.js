/*** Translation ***/
LanguageDirectory="sv";

function getTxt(s)
  {
  switch(s)
    {
    case "Save":return "Spara";
    case "Preview":return unescape("F%F6rhandsgranska");
    case "Full Screen":return unescape("Helsk%E4rm");
    case "Search":return unescape("S%F6k");
    case "Check Spelling":return "Stavningskontroll";
    case "Text Formatting":return "Textformatering";
    case "List Formatting":return "Punkter och numrering";
    case "Paragraph Formatting":return "Formatmallar";
    case "Styles":return "Formatering";
    case "Custom CSS":return "Egen formatering";
    case "Styles & Formatting":return "Formatmallar och formatering";
    case "Style Selection":return "Formatering";
    case "Paragraph":return "Formatmall";
    case "Font Name":return "Teckensnitt";
    case "Font Size":return "Storlek";
    case "Cut":return "Klipp ut";
    case "Copy":return "Kopiera";
    case "Paste":return "Klistra in";
    case "Undo":return unescape("%C5ngra");
    case "Redo":return unescape("G%F6r om");
    case "Bold":return "Fet";
    case "Italic":return "Kursiv";
    case "Underline":return "Understruken";
    case "Strikethrough":return "Genomstruken";
    case "Superscript":return unescape("Upph%F6jd");
    case "Subscript":return unescape("Neds%E4nkt");
    case "Justify Left":return unescape("V%E4nsterjustera");
    case "Justify Center":return "Centrera";
    case "Justify Right":return unescape("H%F6gerjustera");
    case "Justify Full":return "Marginaljustera";
    case "Numbering":return "Numrerad lista";
    case "Bullets":return "Punktlista";
    case "Indent":return unescape("%D6ka indrag");
    case "Outdent":return "Minska indrag";
    case "Left To Right":return unescape("V%E4nster till h%F6ger");
    case "Right To Left":return unescape("H%F6ger till v%E4nster");
    case "Foreground Color":return unescape("F%F6rgrundsf%E4rg");
    case "Background Color":return unescape("Bakgrundsf%E4rg");
    case "Hyperlink":return unescape("Hyperl%E4nk");
    case "Bookmark":return unescape("Bokm%E4rke");
    case "Special Characters":return "Specialtecken";
    case "Image":return "Bild";
    case "Flash":return "Flash";
    case "Media":return "Media";
    case "Content Block":return unescape("Inneh%E5ll"); 
    case "Internal Link":return unescape("Intern l%E4nk");
    case "Internal Image":return "Internal Image";
    case "Object":return "Objekt";
    case "Insert Table":return "Infoga tabell";
    case "Table Size":return "Storlek";
    case "Edit Table":return "Tabellegenskaper";
    case "Edit Cell":return "Cellegenskaper";
    case "Table":return "Tabell";
    case "AutoTable":return "Table Auto Format";
    case "Border & Shading":return "Kantlinjer och fyllning";
    case "Show/Hide Guidelines":return unescape("Visa/d%F6lj st%F6dlinjer");
    case "Absolute":return "Absolut";
    case "Paste from Word":return unescape("Klistra in fr%E5n Word");
    case "Line":return "Linje";
    case "Form Editor":return unescape("Formul%E4r redigeraren");
    case "Form":return  unescape("Formul%E4r");
    case "Text Field":return  unescape("Textf%E4lt");
    case "List":return "Lista";
    case "Checkbox":return "Kryssruta";
    case "Radio Button":return "Radioknapp";
    case "Hidden Field":return unescape("Dolt f%E4lt");
    case "File Field":return unescape("Filf%E4lt");
    case "Button":return "Knapp";
    case "Clean":return "Rensa";
    case "View/Edit Source":return unescape("Visa/redigera k%E4lla");
    case "Tag Selector":return unescape("Taggv%E4ljaren");
    case "Clear All":return "Rensa allt";
    case "Tags":return "Taggar";
    
    case "Heading 1":return "Rubrik 1";
    case "Heading 2":return "Rubrik 2";
    case "Heading 3":return "Rubrik 3";
    case "Heading 4":return "Rubrik 4";
    case "Heading 5":return "Rubrik 5";
    case "Heading 6":return "Rubrik 6";
    case "Preformatted":return unescape("F%F6rformaterad");
    case "Normal (P)":return "Normal (P)";
    case "Normal (DIV)":return "Normal (DIV)";

    case "Size 1":return "1 (8 pt)";
    case "Size 2":return "2 (10 pt)";
    case "Size 3":return "3 (12 pt)";
    case "Size 4":return "4 (14 pt)";
    case "Size 5":return "5 (18 pt)";
    case "Size 6":return "6 (24 pt)";
    case "Size 7":return "7 (36 pt)";

    case "Are you sure you wish to delete all contents?":
      return unescape("%C4r du s%E4ker p%E5 att du vill ta bort allt inneh%E5ll?");
    case "Remove Tag":return "Ta bort tagg";    
    case "Custom Colors":return "Custom Colors";
    case "More Colors...":return "More Colors...";
    case "Box Formatting":return "Box Formatting";
    case "Advanced Table Insert":return "Advanced Table Insert";
    case "Edit Table/Cell":return "Edit Table/Cell";
    case "Print":return "Print";
    case "Paste Text":return "Paste Text";
    case "CSS Builder":return "CSS Builder";
    case "Remove Formatting":return "Remove Formatting";
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
