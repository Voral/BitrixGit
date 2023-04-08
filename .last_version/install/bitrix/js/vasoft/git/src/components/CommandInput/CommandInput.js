import './CommandInput.css';
import {useStorage} from '../../api/useStorage';
import Focus from '../../directives/focus';

const commandStorage = useStorage();

export const CommandInput = {
    directives: {
        Focus
    },
    props: {
        modelValue: String,
        autocomplete: Array
    },
    data() {
        return {
            echo: ''
        }
    },
    emits: ['update:modelValue', 'execute'],
    // language=Vue
    methods: {
        keydown(event) {
            if (event.keyCode === 9) { // Tab
                this.$emit('update:modelValue', this.echo);
                event.preventDefault();
                return false;
            } else if (event.keyCode === 13) { //Enter
                if (this.modelValue !== '') {
                    commandStorage.addCommand(this.modelValue);
                    this.$emit('execute');
                }
                event.preventDefault();
                return false;
            } else if (event.keyCode === 38) { // Up
                this.$emit('update:modelValue', commandStorage.getPrevCommand());
                event.preventDefault();
                return false;
            } else if (event.keyCode === 40) { //Down
                this.$emit('update:modelValue', commandStorage.getNextCommand());
                event.preventDefault();
                return false;
            }
        },
        update() {
            this.echo = this.modelValue + this.search(this.modelValue);
        },
        search(command) {
            let list = [];
            for (let key in this.autocomplete) {
                if (!this.autocomplete.hasOwnProperty(key)) {
                    continue;
                }
                let pattern = new RegExp(key);
                if (command.match(pattern)) {
                    list = this.autocomplete[key];
                }
            }
            let text = command.split(' ').pop();
            let found = '';
            if (text !== '') {
                for (let i = 0; i < list.length; i++) {
                    let value = list[i];
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
    template: `
      <div class="vsg-input">
      <input type="text" class="vsg-input__input"
             v-focus="true"
             @input="$emit('update:modelValue',$event.target.value)"
             v-model="modelValue"
             @keydown="keydown"
             @keyup="update"/>
      <div class="vsg-input__echo">{{echo}}</div>
      </div>
    `
};