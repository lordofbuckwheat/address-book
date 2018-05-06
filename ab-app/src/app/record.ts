export class Record {
    constructor() {
        this.name = '';
        this.phone = '';
        this.email = '';
    }
    id?: number;
    name: string;
    phone: string;
    email: string;
    reset() {
        this.id = null;
        this.name = '';
        this.phone = '';
        this.email = '';
    }
    clone(): Record {
        return Object.assign({}, this);
    }
}