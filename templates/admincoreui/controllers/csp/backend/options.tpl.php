<?php

    $this->addMainTplJSName([
        'vendors/vue/vue.min',
        'admin-csp',
    ]);

    $this->addBreadcrumb(LANG_OPTIONS);

?>
<div id="csp_options_gen" class="mb-2">
    <?php
        $this->renderForm($form, $options, [
            'action' => '',
            'submit' => ['title' => LANG_SAVE],
            'method' => 'post'
        ], $errors);
    ?>
    <div class="alert alert-primary">
        <h5 class="alert-heading"><?php echo LANG_CSP_GEN; ?></h5>
        <p class="mb-0"><?php echo LANG_CSP_GEN_HINT; ?></p>
    </div>
    <div class="row align-items-stretch no-gutters mr-n3" v-cloak>
        <template v-for="(value, d) in srcDirectives">
            <div class="col-lg-3 mb-3" :id="'directive_'+d"  v-if="rules[d] && isShow(d)">
                <div class="card h-100 mb-0 mr-3 animated fadeIn">
                    <div class="card-header d-flex justify-content-between align-items-center" :title="hint(d)">
                        <span>{{d}}</span>
                        <a href="#" class="text-danger small" @click.prevent="removeBlock(d)" v-if="d != 'default-src'" title="<?php echo LANG_DELETE; ?>">
                            <?php html_svg_icon('solid', 'minus-circle'); ?>
                        </a>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox" v-for="b in srcDirectives[d].values" :title="hintd(b)">
                                <input type="checkbox" class="custom-control-input" :id="'check'+d+'-'+b" v-model="rules[d][b]">
                                <label class="custom-control-label" :for="'check'+d+'-'+b">
                                    {{b}}
                                </label>
                            </div>
                        </div>
                        <div class="form-group mb-0 mt-auto">
                            <input class="form-control" type="text" placeholder="<?php echo LANG_CSP_DOMAIN_PLACEHOLDER; ?>" v-model="rules[d].domain">
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="col-lg-3 mb-3" v-if="disabledDirectives.length>0">
            <div class="card h-100 mb-0 mr-3 bg-transparent shadow-none border-0">
                <div class="card-body d-flex justify-content-center align-items-center">
                    <a href="#" class="text-success h4 m-0 d-flex align-items-center text-decoration-none" @click.prevent="addBlockForm" v-if="!showAddSelect">
                        <?php html_svg_icon('solid', 'plus-circle'); ?>
                        <span class="ml-2"><?php echo LANG_CSP_ADD_BLOCK; ?></span>
                    </a>
                    <div v-if="showAddSelect">
                        <select class="form-control custom-select" @change="addBlock($event.target.value)">
                            <option value=""><?php echo LANG_SELECT; ?></option>
                            <option v-for="value in disabledDirectives" :value="value">{{value}}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ob_start(); ?>
<script>
    icms.adminCsp.initApp(<?php echo json_encode(array_merge($options, ['hints' => LANG_CSP_HINTS, 'dhints' => LANG_CSP_DIRECTIVES]), JSON_UNESCAPED_UNICODE); ?>);
</script>
<?php $this->addBottom(ob_get_clean()); ?>