var icms = icms || {};
icms.adminCsp = (function () {

    let self = this;

    this.app = {};

    this.initApp = function(data){

        const app = {
            data() {
                return {...data,
                    showAddSelect: false,
                    cspInput: '',
                    rules: {},
                    directiveMapping: {
                        all: '*',
                        none: "'none'",
                        self: "'self'",
                        data: 'data:',
                        nonce: "'nonce-{nonce}'",
                        'strict-dynamic': "'strict-dynamic'",
                        'unsafe-inline': "'unsafe-inline'",
                        'unsafe-hashes': "'unsafe-hashes'",
                        'unsafe-eval': "'unsafe-eval'"
                    },
                    srcDirectives: {
                        'default-src': {enable: true, values: ['none', 'all', 'self', 'data', 'unsafe-inline', 'unsafe-eval', 'unsafe-hashes', 'strict-dynamic']},
                        'script-src': {enable: false, values: ['none', 'all', 'self', 'data', 'unsafe-inline', 'unsafe-eval', 'unsafe-hashes', 'nonce', 'strict-dynamic']},
                        'script-src-elem': {enable: false, values: ['none', 'all', 'self', 'data', 'unsafe-inline', 'unsafe-eval', 'unsafe-hashes', 'nonce']},
                        'script-src-attr': {enable: false, values: ['none', 'all', 'self', 'data', 'unsafe-inline', 'unsafe-eval', 'unsafe-hashes', 'nonce']},
                        'style-src': {enable: false, values: ['none', 'all', 'self', 'data', 'unsafe-inline', 'nonce', 'unsafe-hashes']},
                        'style-src-elem': {enable: false, values: ['none', 'all', 'self', 'data', 'unsafe-inline', 'nonce', 'unsafe-hashes']},
                        'style-src-attr': {enable: false, values: ['none', 'all', 'self', 'data', 'unsafe-inline', 'nonce', 'unsafe-hashes']},
                        'img-src': {enable: false, values: ['none', 'all', 'self', 'data']},
                        'font-src': {enable: false, values: ['none', 'all', 'self', 'data']},
                        'connect-src': {enable: false, values: ['none', 'all', 'self']},
                        'media-src': {enable: false, values: ['none', 'all', 'self']},
                        'object-src': {enable: false, values: ['none', 'all', 'self']},
                        'child-src': {enable: false, values: ['none', 'all', 'self']},
                        'frame-src': {enable: false, values: ['none', 'all', 'self']},
                        'worker-src': {enable: false, values: ['none', 'all', 'self']},
                        'frame-ancestors': {enable: false, values: ['none', 'all', 'self']},
                        'form-action': {enable: false, values: ['none', 'all', 'self']},
                        'manifest-src': {enable: false, values: ['none', 'all', 'self']}
                    }
                };
            },
            methods: {
                initializeDefaultRules() {
                    Object.keys(this.srcDirectives).forEach(key => {
                        this.clearDirective(key);
                    });
                },
                clearDirective(key) {
                    this.rules[key] = {};
                    this.srcDirectives[key].values.forEach(value => {
                        this.rules[key][value] = false;
                    });
                    this.rules[key].domain = '';
                },
                hint(key) {
                    return this.hints[key] ? this.hints[key] : '';
                },
                hintd(key) {
                    return this.dhints[key] ? this.dhints[key] : '';
                },
                addBlockForm() {
                    this.showAddSelect = true;
                },
                addBlock(directive) {
                    this.showAddSelect = false;
                    this.srcDirectives[directive].enable = true;
                },
                removeBlock(directive) {
                    this.srcDirectives[directive].enable = false;
                    this.clearDirective(directive);
                },
                isShow(directive) {

                    const is_enable = this.srcDirectives[directive].enable;

                    const group = this.getDirectiveGroup(directive);

                    return is_enable || group.length > 0;
                },
                getDirectiveGroup(key) {

                    const group = [];

                    if (!this.rules[key]) {
                        return group;
                    }

                    this.srcDirectives[key].values.forEach(v => {
                        if (this.rules[key][v]) {
                            if (!group.length) {
                                group.push(key);
                            }
                            const value = this.directiveMapping[v] || v;
                            group.push(value);
                        }
                    });

                    if (this.rules[key].domain) {
                        if (!group.length) {
                            group.push(key);
                        }
                        group.push(this.rules[key].domain);
                    }

                    return group;
                }
            },
            computed: {
                disabledDirectives() {

                    const output = [];

                    Object.keys(this.srcDirectives).forEach(key => {

                        if (!this.isShow(key)) {
                            output.push(key);
                        }

                    });

                    return output;
                },
                cspString() {

                    const output = [];

                    Object.keys(this.srcDirectives).forEach(key => {

                        const group = this.getDirectiveGroup(key);

                        output.push({key, string: group.join(' ')});
                    });

                    return output
                            .filter(i => !!i.string)
                            .map(i => i.string)
                            .join('; ');
                }
            },

            watch: {
                cspInput() {

                    this.rules = {};

                    this.initializeDefaultRules();

                    const directives = this.cspInput.split(';');

                    directives.forEach(directive => {

                        const options = directive.trim().split(' ');

                        let directiveName = '';

                        options.forEach((option, idx) => {

                            if (!option) {
                                return;
                            }

                            if (!directiveName) {
                                directiveName = option;
                                return;
                            }

                            if (!this.rules[directiveName]) {
                                console.log('Directive not found: ' + directiveName);
                                return;
                            }

                            for (let key in this.directiveMapping) {
                                if (option === this.directiveMapping[key]) {
                                    this.rules[directiveName][key] = true;
                                    return;
                                }
                            }

                            if (!this.rules[directiveName].domain) {
                                this.rules[directiveName].domain = option;
                            } else {
                                this.rules[directiveName].domain += ' '+option;
                            }
                        });

                        if (this.rules[directiveName] && !this.rules[directiveName].domain.length) {
                            this.rules[directiveName].domain = '';
                        }
                    });
                }
            },
            mounted() {
                this.cspInput = this.csp_str;
                this.initializeDefaultRules();
            }
        };

        const vapp = Vue.createApp(app);

        self.app = vapp.mount('#csp_options_gen');
    };

    return this;

}).call(icms.adminCsp || {});