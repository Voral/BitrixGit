import './Console.css';
import {CommandLine} from '../CommandLine/CommandLine';
import {useClient} from '../../api/client';

const client = useClient();

export const Console = {
    components: {
        CommandLine
    },
    data(): Object {
        return {
            user: '',
            path: '',
            autocomplete: [],
            output: '',
            error: '',
        }
    },
    methods: {
        scroll() {
        },
        execTagLog(){
            console.log('execTagLog');
            this.exec('git log --no-walk --tags --pretty="%h %d %s%n%b" --decorate=short');
        },
        exec(command) {
            console.log('exec', command);
            client.execute(command).then(this.onData).catch(this.onError);
        },
        onError(response) {
            if (response.hasOwnProperty('errors') && response.errors.length > 0) {
                let i = 0, cnt = response.errors.length;
                let errorText = '';
                for (; i < cnt; ++i) {
                    let error = response.errors[i];
                    if (error.hasOwnProperty('code') && error.hasOwnProperty('message') && error.code >= 5000) {
                        errorText += "\n" + response.errors[i].message;
                    }
                }
                this.error = errorText;
            }
        },
        onData(response) {
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
    updated() {
        let container = this.$el.querySelector('#vsg-terminal');
        container.scrollTop = container.scrollHeight;
    },
    beforeMount() {
        client.init().then(this.onData);
    },
    // language=Vue
    template: `
      <div class="vsg-console">
      <div class="vsg-console__buttons">
        <button @click="execTagLog">Tag log</button>
      </div>
      <pre id="vsg-terminal" class="vsg-terminal" v-show="output.length>0">{{output}}</pre>
      <pre class="vsg-error" v-show="error.length>0">{{error}}</pre>
      <CommandLine :user="user" :path="path" :autocomplete="autocomplete" @execute="exec"/>
      </div>
    `
};