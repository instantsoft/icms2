/*** Translation ***/
LanguageDirectory="fr";

function getTxt(s)
  {
  switch(s)
    {
    case "Save":return "Enregistrer";
    case "Preview":return "Aper&ccedil;u";
    case "Full Screen":return "Afficher en plein &eacute;cran";
    case "Search":return "Rechercher et remplacer";
    case "Check Spelling":return "V&eacute;rification d'orthographe";
    case "Text Formatting":return "Cr&eacute;er un style seulement pour ce document";
    case "List Formatting":return "S&eacute;lectionner un format de puces et num&eacute;ros";
    case "Paragraph Formatting":return "Mise en forme du paragraphe courant";
    case "Styles":return "Selectionner un style";
    case "Custom CSS":return "Feuille de styles CSS personnalis&eacute;e";
    case "Styles & Formatting":return "Styles CSS et mise en forme";
    case "Style Selection":return "S&eacute;lectionner un style";
    case "Paragraph":return "Paragraphe";
    case "Font Name":return "Nom de Police";
    case "Font Size":return "Taille de Police";
    case "Cut":return "Couper";
    case "Copy":return "Copier";
    case "Paste":return "Coller";
    case "Undo":return "Annuler l&#8217;action";
    case "Redo":return "R&eacute;tablir l&#8217;action annul&eacute;e";
    case "Bold":return "Gras";
    case "Italic":return "Italique";
    case "Underline":return "Soulign&eacute;";
    case "Strikethrough":return "Barr&eacute;";
    case "Superscript":return "Placer en Exposant";
    case "Subscript":return "Placer en Indice";
    case "Justify Left":return "Aligner &agrave; gauche";
    case "Justify Center":return "Aligner au centre";
    case "Justify Right":return "Aligner &agrave; droite";
    case "Justify Full":return "Justifier";
    case "Numbering":return "Num&eacute;ros";
    case "Bullets":return "Puces";
    case "Indent":return "Augmenter le retrait";
    case "Outdent":return "Diminuer le retrait";
    case "Left To Right":return "De gauche &aacute; droite";
    case "Right To Left":return "De droite &aacute; gauche";
    case "Foreground Color":return "Couleur des caract&egrave;res";
    case "Background Color":return "couleur de l&#8217;arri&egrave;re plan";
    case "Hyperlink":return "Ins&eacute;rer un lien hypertexte";
    case "Bookmark":return "Ins&eacute;rer un signet";
    case "Special Characters":return "Caract&egrave;res sp&eacute;ciaux";
    case "Image":return "Image";
    case "Flash":return "Flash";
    case "Media":return "Media";
    case "Content Block":return "Bloc HTML du Contenu"; 
    case "Internal Link":return "Lien Interne";
    case "Internal Image":return "Internal Image";
    case "Object":return "Objet";
    case "Insert Table":return "Ins&eacute;rer un Tableau";
    case "Table Size":return "Taille";
    case "Edit Table":return "Modifier le Tableau s&eacute;lectionn&eacute;";
    case "Edit Cell":return "Modifier la Cellule s&eacute;lectionn&eacute;e";
    case "Table":return "Tableau";
    case "AutoTable":return "Tableau Format Auto";
    case "Border & Shading":return "Bordures et Ombrage";
    case "Show/Hide Guidelines":return "Afficher/Masquer les bordures du tableau";
    case "Absolute":return "Ins&eacute;rer la s&eacute;lection dans un cadre de texte";
    case "Paste from Word":return "Coller un document Word";
    case "Line":return "Ins&eacute;rer une Ligne";
    case "Form Editor":return "Editeur de formulaire";
    case "Form":return "Formulaire";
    case "Text Field":return "Champ texte";
    case "List":return "Liste";
    case "Checkbox":return "Case &agrave; cocher";
    case "Radio Button":return "Radio Bouton";
    case "Hidden Field":return "Champ cach&eacute;";
    case "File Field":return "Champ de fichier";
    case "Button":return "Bouton";
    case "Clean":return "Supprimer l&acute;enrichissement des caract&egrave;res";
    case "View/Edit Source":return "Afficher/Modifier le code HTML";
    case "Tag Selector":return "Selecteur de balise";
    case "Clear All":return "Effacer tout le contenu";
    case "Tags":return "Balises HTML";

    case "Heading 1":return "Titre 1";
    case "Heading 2":return "Titre 2";
    case "Heading 3":return "Titre 3";
    case "Heading 4":return "Titre 4";
    case "Heading 5":return "Titre 5";
    case "Heading 6":return "Titre 6";
    case "Preformatted":return "Pr&eacute;format&eacute;";
    case "Normal (P)":return "Normal (P)";
    case "Normal (DIV)":return "Normal (DIV)";

    case "Size 1":return "Taille 1";
    case "Size 2":return "Taille 2";
    case "Size 3":return "Taille 3";
    case "Size 4":return "Taille 4";
    case "Size 5":return "Taille 5";
    case "Size 6":return "Taille 6";
    case "Size 7":return "Taille 7";

	case "Are you sure you wish to delete all contents?":
      return "Etes vous s&ucirc;r(e) de vouloir supprimer tout le contenu ?";
    case "Remove Tag":return "Supprimer la balise";   
    case "Custom Colors":return "Couleurs personnalis&eacute;es";
    case "More Colors...":return "Autres Couleurs...";
    case "Box Formatting":return "Mise en forme du bloc...";
    case "Advanced Table Insert":return "Am&eacute;nagement des tableaux...";//
    case "Edit Table/Cell":return "Modifier le Tableau/Cellule";
    case "Print":return "Imprimer";
    case "Paste Text":return "Coller au format texte";
    case "CSS Builder":return "Constructeur de CSS";
    case "Remove Formatting":return "Supprimer la mise en forme";
    case "Table Dimension Text": return "Dimension Texte";
    case "Table Advance Link": return "Avanc&eacute;";

    case "Fonts": return "Polices";
    case "Text": return "Texte";
    case "Link": return "Lien";
    case "YoutubeVideo": return "Vid&eacute;o Youtube";
    case "Search & Replace": return "Rechercher et remplacer";
    case "HTML Editor": return "Editeur HTML";
    case "Emoticons": return "Emoticons";
    case "PasteWarning": return "Merci d'utiliser le raccourci CTRL-V pour coller votre contenu."; /*Your browser security settings don't permit this operation.*/
    case "Quote": return "Citation";
    default:return "";
    }
  }
