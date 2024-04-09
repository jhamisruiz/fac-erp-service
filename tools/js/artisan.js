class ArtisanCommand {
    static artisan() {
        const { spawn } = require('child_process');
        const chalk = require('chalk');
        const { Command } = require('./../../node_modules/commander');
        const program = new Command();
        console.log(chalk.green(`Starting to build!`));
        program
            .version('1.0.0')
            .command('c [component]')
            .alias('component')
            .description('Genera un componente')
            .option('--ns <ns>', 'Estructura de directorios y archivos', 'App')
            .option('--path <path>', 'Ruta del componente', null)
            .option('-p <p>', 'Ruta del componente', null)
            .action((component, options) => {
                const { ns: optNs, path: optPath, p: optP } = options;
                if (!component) {
                    console.error(chalk.red('Error: Debe especificar el nombre del componente.'));
                    process.exit(1);
                }
                console.log(chalk.green(`\n ${JSON.stringify(component)}: component!`));
                console.log(chalk.green(`\n ${JSON.stringify(options)} options!`));
                const phpScript = spawn('php', [
                    'artisan',
                    'c',
                    `${component}`,
                    `--ns=${optNs}`,
                    `--path=${optPath ? optPath : optP}`
                ], {
                    stdio: 'inherit'
                });
                phpScript.on('error', (err) => {
                    console.error(chalk.red(`Error: ${err.message}`));
                    process.exit(1);
                });
                phpScript.on('exit', (code) => {
                    if (code === 0) {
                        console.log(chalk.green(`\n ${component} created!`));
                    }
                });
            });
        program.parse(process.argv);
    }
}
module.exports = ArtisanCommand;