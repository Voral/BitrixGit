export function useClient() {
    const get = function (method: String, data: Object) {
        return BX.ajax.runAction('vasoft:git.ConsoleController.' + method, {
            json: data
        });
    }
    const init = function () {
        return get('environment', {});
    }
    const execute = function (command) {
        return get('execute', {command: command});
    }
    return {init, execute};
}