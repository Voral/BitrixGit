/**
 * Консоль Git
 *
 * @package vasoft.git
 * @subpackage js.module
 * @copyright 2023
 */

import {BitrixVue} from 'ui.vue3';
import {Console} from './components/Console/Console';


export class GitConsole {
    #application;
    #rootNode;
    constructor(rootNode): void {
        this.#rootNode = document.querySelector(rootNode);
    }
    start(): void {
        this.#application = BitrixVue.createApp({
            name: 'Git Console Application',
            components: {
                Console
            },
            template: '<Console/>'
        })
        this.#application.mount(this.#rootNode);
    }
}
