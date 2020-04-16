<?php if (!empty($this->options['logo'])){ ?>
#layout header #logo a, #layout header #logo > span {
    background-image: url("<?php echo $config->upload_root . $this->options['logo']['original']; ?>") !important;
    background-size: contain;
}
<?php } ?>
#body section {
    float: <?php echo $this->options['aside_pos']=='left' ? 'right' : 'left'; ?> !important;
}
#body aside {
    float: <?php echo $this->options['aside_pos']=='left' ? 'left' : 'right'; ?> !important;
}
#body aside .menu li ul {
    <?php echo $this->options['aside_pos']=='left' ? 'right' : 'left'; ?>: auto !important;
    <?php if ($this->options['aside_pos']=='left'){ ?>left: 210px;<?php } ?>
}
@media screen and (max-width: 980px) {
    #layout { width: 98% !important; min-width: 0 !important; }
}
