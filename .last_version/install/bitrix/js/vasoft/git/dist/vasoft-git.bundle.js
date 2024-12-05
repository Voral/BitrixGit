this.BX = this.BX || {};
(function (exports,ui_vue3) {
    'use strict';

    function useStorage() {
      var maxHistory = 100;
      var commands = [];
      var position = 0;
      var load = function load() {
        var ls = localStorage['commands'];
        if (ls) {
          commands = JSON.parse(ls);
        }
      };
      load();
      var addCommand = function addCommand(command) {
        if (commands.length > maxHistory) {
          commands.shift();
        }
        commands.push(command);
        localStorage['commands'] = JSON.stringify(commands);
      };
      var getPrevCommand = function getPrevCommand() {
        ++position;
        return position <= commands.length ? commands[position - 1] : '';
      };
      var getNextCommand = function getNextCommand() {
        --position;
        return position >= 1 ? commands[position - 1] : '';
      };
      return {
        getNextCommand: getNextCommand,
        getPrevCommand: getPrevCommand,
        addCommand: addCommand
      };
    }

    var Focus = {
      mounted: function mounted(el, binding) {
        if (binding.value) {
          el.focus();
        }
      }
    };

    var commandStorage = useStorage();
    var CommandInput = {
      directives: {
        Focus: Focus
      },
      props: {
        modelValue: String,
        autocomplete: Array
      },
      data: function data() {
        return {
          echo: ''
        };
      },
      emits: ['update:modelValue', 'execute'],
      // language=Vue
      methods: {
        keydown: function keydown(event) {
          if (event.keyCode === 9) {
            // Tab
            this.$emit('update:modelValue', this.echo);
            event.preventDefault();
            return false;
          } else if (event.keyCode === 13) {
            //Enter
            if (this.modelValue !== '') {
              commandStorage.addCommand(this.modelValue);
              this.$emit('execute');
            }
            event.preventDefault();
            return false;
          } else if (event.keyCode === 38) {
            // Up
            this.$emit('update:modelValue', commandStorage.getPrevCommand());
            event.preventDefault();
            return false;
          } else if (event.keyCode === 40) {
            //Down
            this.$emit('update:modelValue', commandStorage.getNextCommand());
            event.preventDefault();
            return false;
          }
        },
        update: function update() {
          this.echo = this.modelValue + this.search(this.modelValue);
        },
        search: function search(command) {
          var list = [];
          for (var key in this.autocomplete) {
            if (!this.autocomplete.hasOwnProperty(key)) {
              continue;
            }
            var pattern = new RegExp(key);
            if (command.match(pattern)) {
              list = this.autocomplete[key];
            }
          }
          var text = command.split(' ').pop();
          var found = '';
          if (text !== '') {
            for (var i = 0; i < list.length; i++) {
              var value = list[i];
              if (value.length > text.length && value.substring(0, text.length) === text) {
                found = value.substring(text.length, value.length);
                break;
              }
            }
          }
          return found;
        }
      },
      // language=Vue
      template: "\n      <div class=\"vsg-input\">\n      <input type=\"text\" class=\"vsg-input__input\"\n             v-focus=\"true\"\n             @input=\"$emit('update:modelValue',$event.target.value)\"\n             v-model=\"modelValue\"\n             @keydown=\"keydown\"\n             @keyup=\"update\"/>\n      <div class=\"vsg-input__echo\">{{echo}}</div>\n      </div>\n    "
    };

    var CommandLine = {
      components: {
        CommandInput: CommandInput
      },
      props: {
        user: String,
        path: String,
        autocomplete: Array
      },
      data: function data() {
        return {
          command: ''
        };
      },
      emits: ['execute'],
      methods: {
        exec: function exec() {
          this.$emit('execute', this.command);
          this.command = '';
        }
      },
      // language=Vue
      template: "\n      <div class=\"vsg-cmd\">\n      <div class=\"vsg-cmd__info\">{{path}}</div>\n      <div class=\"vsg-cmd__info\">{{user}}$</div>\n      <CommandInput class=\"vsg-cmd__input\" v-model=\"command\" :autocomplete=\"autocomplete\" @execute=\"exec\"/>\n      </div>\n    "
    };

    function useClient() {
      var get = function get(method, data) {
        return BX.ajax.runAction('vasoft:git.ConsoleController.' + method, {
          json: data
        });
      };
      var init = function init() {
        return get('environment', {});
      };
      var execute = function execute(command) {
        return get('execute', {
          command: command
        });
      };
      return {
        init: init,
        execute: execute
      };
    }

    var client = useClient();
    var Console = {
      components: {
        CommandLine: CommandLine
      },
      data: function data() {
        return {
          user: '',
          path: '',
          autocomplete: [],
          output: '',
          error: ''
        };
      },
      methods: {
        scroll: function scroll() {},
        execTagLog: function execTagLog() {
          console.log('execTagLog');
          this.exec('git log --no-walk --tags --pretty="%h %d %s%n%b" --decorate=short');
        },
        exec: function exec(command) {
          console.log('exec', command);
          client.execute(command).then(this.onData)["catch"](this.onError);
        },
        onError: function onError(response) {
          if (response.hasOwnProperty('errors') && response.errors.length > 0) {
            var i = 0,
              cnt = response.errors.length;
            var errorText = '';
            for (; i < cnt; ++i) {
              var error = response.errors[i];
              if (error.hasOwnProperty('code') && error.hasOwnProperty('message') && error.code >= 5000) {
                errorText += "\n" + response.errors[i].message;
              }
            }
            this.error = errorText;
          }
        },
        onData: function onData(response) {
          if (!response.hasOwnProperty('data')) {
            return;
          }
          if (response.data.hasOwnProperty('user')) {
            this.user = response.data.user;
          }
          if (response.data.hasOwnProperty('path')) {
            this.path = response.data.path;
          }
          if (response.data.hasOwnProperty('autocomplete')) {
            this.autocomplete = response.data.autocomplete;
          }
          if (response.data.hasOwnProperty('output')) {
            this.output = response.data.output;
          }
          if (response.data.hasOwnProperty('error')) {
            this.error = response.data.error;
          }
        }
      },
      updated: function updated() {
        var container = this.$el.querySelector('#vsg-terminal');
        container.scrollTop = container.scrollHeight;
      },
      beforeMount: function beforeMount() {
        client.init().then(this.onData);
      },
      // language=Vue
      template: "\n      <div class=\"vsg-console\">\n      <div class=\"vsg-console__buttons\">\n        <button @click=\"execTagLog\">Tag log</button>\n      </div>\n      <pre id=\"vsg-terminal\" class=\"vsg-terminal\" v-show=\"output.length>0\">{{output}}</pre>\n      <pre class=\"vsg-error\" v-show=\"error.length>0\">{{error}}</pre>\n      <CommandLine :user=\"user\" :path=\"path\" :autocomplete=\"autocomplete\" @execute=\"exec\"/>\n      </div>\n    "
    };

    function _classPrivateFieldInitSpec(obj, privateMap, value) { _checkPrivateRedeclaration(obj, privateMap); privateMap.set(obj, value); }
    function _checkPrivateRedeclaration(obj, privateCollection) { if (privateCollection.has(obj)) { throw new TypeError("Cannot initialize the same private elements twice on an object"); } }
    var _application = /*#__PURE__*/new WeakMap();
    var _rootNode = /*#__PURE__*/new WeakMap();
    var GitConsole = /*#__PURE__*/function () {
      function GitConsole(rootNode) {
        babelHelpers.classCallCheck(this, GitConsole);
        _classPrivateFieldInitSpec(this, _application, {
          writable: true,
          value: void 0
        });
        _classPrivateFieldInitSpec(this, _rootNode, {
          writable: true,
          value: void 0
        });
        babelHelpers.classPrivateFieldSet(this, _rootNode, document.querySelector(rootNode));
      }
      babelHelpers.createClass(GitConsole, [{
        key: "start",
        value: function start() {
          babelHelpers.classPrivateFieldSet(this, _application, ui_vue3.BitrixVue.createApp({
            name: 'Git Console Application',
            components: {
              Console: Console
            },
            template: '<Console/>'
          }));
          babelHelpers.classPrivateFieldGet(this, _application).mount(babelHelpers.classPrivateFieldGet(this, _rootNode));
        }
      }]);
      return GitConsole;
    }();

    exports.GitConsole = GitConsole;

}((this.BX.Vasoft = this.BX.Vasoft || {}),BX.Vue3));
//# sourceMappingURL=vasoft-git.bundle.js.map
