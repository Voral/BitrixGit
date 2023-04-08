import './CommandLine.css';
import {CommandInput} from '../CommandInput/CommandInput';

export const CommandLine = {
    components: {
        CommandInput
    },
    props: {
        user: String,
        path: String,
        autocomplete: Array
    },
    data() {
        return {
            command: ''
        }
    },
    emits: ['execute'],
    methods: {
        exec() {
            this.$emit('execute', this.command);
            this.command = '';
        },
    },
    // language=Vue
    template: `
      <div class="vsg-cmd">
      <div class="vsg-cmd__info">{{path}}</div>
      <div class="vsg-cmd__info">{{user}}$</div>
      <CommandInput class="vsg-cmd__input" v-model="command" :autocomplete="autocomplete" @execute="exec"/>
      </div>
    `
};