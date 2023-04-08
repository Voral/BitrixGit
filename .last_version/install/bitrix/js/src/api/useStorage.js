export function useStorage() {
    const maxHistory = 100;
    let commands = [];
    let position = 0;

    const load = () => {
        const ls = localStorage['commands'];
        if (ls) {
            commands = JSON.parse(ls);
        }
    }
    load();

    const addCommand = function (command) {
        if (commands.length > maxHistory) {
            commands.shift();
        }
        commands.push(command);
        localStorage['commands'] = JSON.stringify(commands);
    };

    const getPrevCommand = function () {
        ++position;
        return position <= commands.length ? commands[position - 1] : '';
    }
    const getNextCommand = function () {
        --position;
        return position >= 1 ? commands[position - 1] : '';
    }
    return {getNextCommand, getPrevCommand, addCommand};
}