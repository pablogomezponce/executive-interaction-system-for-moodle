class Widget {

    constructor(title, content, kind, schema) {

        this._title = title;
        this._content = content;
        this._kind = kind;
        this._schema = schema;

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

    render(){

        if (this._content === null) {this.process()}

        return "<div class='card'>" +
            "" +
            "<div class='card-divider'>" +
            this._title +
            "</div>" +
            "" +
            "<div>" +
            this._content +
            "</div>" +
            "" +
            "</div>";
    }

    process(){
        switch (this.kind)
        {
            default:
                this.content = JSON.stringify(this._schema);
                break;
        }
    }

}