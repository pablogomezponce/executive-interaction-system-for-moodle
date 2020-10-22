class Widget {

    constructor(title, content, kind, schema) {

        this._title = title;
        this._content = content;
        this._kind = kind;
        this._schema = schema;
        this._style = {
            general: {
                maxWidth: '50%',
                width:'auto',
                minWidth: '300px',
                maxHeight: '300px',
                margin: '10px',
                borderRadius: '50px',
            },
            title: {
                justifyContent: 'center',
            },
            content: {
                horizontalPadding: '25px',
                overflowX: 'scroll',
                height: '100%',
                justifyContent: 'center',
                alignItems: 'center',
                display: 'flex',
            }
        };
    }


    get style() {
        return this._style;
    }

    set style(value) {
        this._style = value;
    }

    get title() {
        return this._title;
    }

    set title(value) {
        this._title = value;
    }

    get content() {
        return this._content;
    }

    set content(value) {
        this._content = value;
    }

    get kind() {
        return this._kind;
    }

    set kind(value) {
        this._kind = value;
    }

    get schema() {
        return this._schema;
    }

    set schema(value) {
        this._schema = value;
    }

    render() {

        if (this._content === null) {
            this.process()
        }

        return "<div class='card' style='max-width: " + this._style.general.maxWidth + ";" +
            " max-height: " + this._style.general.maxHeight + ";" +
            " min-width: " +this._style.general.minWidth + ";" +
            " width: " + this._style.general.width + ";" +
            " margin: " + this._style.general.margin + ";" +
            " border-radius: " + this._style.general.borderRadius + "'>" +
            "" +
            "<div class='card-divider'" +
            "style='justify-content: " + this._style.title.justifyContent + "'" +
            ">" +
            "<p style='text-align: center'>" +
            this._title +
            "</p>" +
            "</div>" +
            "" +
            "<div style='"+
            "overflow-x: " + this._style.content.overflowX + ";" +
            "overflow-y:" + this._style.content.overflowX + ";" +
            "margin: " + this._style.content.horizontalPadding + ";" +
            "display: " + this._style.content.display + ";" +
            "width: calc(100% - (2 * " + this._style.content.horizontalPadding + "));" +
            "justify-content: "+ this._style.content.justifyContent + ";" +
            "height: 100%;" +
            "align-self: center'>" +

            this._content +
            "</div>" +
            "" +
            "</div>";
    }

    process() {
        switch (this.kind) {
            case 'COUNTER':
                if (this._schema.length > 0) {
                    if (this._schema[0].amount) {
                        this.content = "<p style='align-self: center; font-size: 4em'>"+ this._schema[0].amount+"</p>";
                    }
                } else {
                    this.content = "No hay resultados";
                }
                break;
            case 'LIST':
                let table = document.createElement('TABLE');
                if (this._schema.length > 0) {


                    let headers = [];
                    let keys = Object.keys(this._schema[0]);

                    for (let i = 0; i < keys.length; i++) {
                        let key = keys[i];
                        let condition = /[^0-9]/;
                        if (condition.test(key)){
                            headers.push(key);
                        }
                    }

                    console.log(headers);

                    let header = document.createElement('THEAD');
                    let tr = document.createElement('TR');

                    for (let i = 0; i < headers.length; i++) {
                        let header = headers[i];

                        let th = document.createElement('TH');
                        th.innerText = header;
                        tr.append(th);
                    }
                    
                    let tbody = document.createElement('TBODY');

                    for (let i = 0; i < this._schema.length; i++) {
                        let row = this._schema[i];

                        let tr = document.createElement('TR');

                        for (let j = 0; j < headers.length; j++) {
                            let location = headers[j];

                            let td = document.createElement('TD');

                            td.innerText = row[location];
                            tr.append(td);
                        }

                        tbody.append(tr);
                    }
                    
                    header.append(tr);
                    table.append(header);
                    table.append(tbody);

                }

                this.content = table.outerHTML;

                break;

            default:
                this.content = JSON.stringify(this._schema);
                break;
        }
    }

}