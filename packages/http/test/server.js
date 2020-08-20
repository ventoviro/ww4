/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

const http = require('http');
const uri = require('url');
const util = require('util');

const port = process.argv.length >= 3 ? process.argv[2] : 8126;

const server = http.createServer((req, res) => {
    let body = '';

    // Receive each chunk of the request body
    req.addListener('data', chunk => {
        body += chunk;
    });
    req.addListener('end', () => {
        req.body = body;

        const app = new App();

        app.run(req, res);

        res.end();
    });
});

server.listen(port);

class App {
    static camelize(str) {
        return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function(word, index) {
            return index === 0 ? word.toLowerCase() : word.toUpperCase();
        }).replace(/\s+/g, '');
    }

    /**
     * @param {IncomingMessage} req
     * @param {ServerResponse} res
     */
    run(req, res) {
        // const parts = uri.parse(req.url, false);
        // let route = parts.pathname.substr(1) || 'index';
        // route.replace(/(\/|_)/, ' ');
        // route = this.constructor.camelize(route);
        //
        // if (!this[route]) {
        //     res.writeHead(404);
        //     return res;
        // }

        return this.index(req, res);
    }

    /**
     * @param {IncomingMessage} req
     * @param {ServerResponse} res
     */
    index(req, res) {
        const msgs = [
            `${req.method} ${req.protocol || 'http'}://${req.headers.host}${req.url}`,
            'HEADERS',
            util.inspect(req.headers),
            'BODY',
            req.body,
        ];

        res.write(msgs.join("\n"));
    }
}
