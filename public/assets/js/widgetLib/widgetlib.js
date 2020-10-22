class Widget {

    constructor(title, content, kind, schema) {

        this._title = title;
        this._content = content;
        this._kind = kind;
        this._schema = schema;
        this._style = {
            general: {
                maxWidth: '300px',
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
            " margin: "+this._style.general.margin+";" +
            " border-radius: "+this._style.general.borderRadius+"'>" +
            "" +
            "<div class='card-divider'" +
            "style='justify-content: "+this._style.title.justifyContent+"'" +
            ">" +
            "<p style='text-align: center'>" +
            this._title +
            "</p>" +
            "</div>" +
            "" +
            "<div style='padding-left: "+this._style.content.horizontalPadding+";" +
            "padding-right: "+this._style.content.horizontalPadding+";" +
            "padding-bottom:" + this._style.content.horizontalPadding + ";"+
            "overflow: "+this._style.content.overflowX+";" +
            "height:"+this._style.content.height+" '>" +
            this._content +
            "</div>" +
            "" +
            "</div>";
    }

    process() {
        switch (this.kind) {
            case 'COUNTER':
                console.log(this._schema);
                if (this._schema.length > 0){
                    if (this._schema[0].amount){
                    this.content = this._schema[0].amount;
                    }
                } else {
                    this.content = "No hay resultados";
                }
                break;

            default:
                this.content = JSON.stringify(this._schema);
                break;
        }
    }

}