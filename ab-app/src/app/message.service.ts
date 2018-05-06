import { Injectable } from '@angular/core';

@Injectable({
    providedIn: 'root'
})
export class MessageService {

    public isLoading: boolean = false;
    public message: string;
    public success: boolean;

    constructor() {}

    showLoading() {
        this.isLoading = true;
    }

    hideLoading() {
        this.isLoading = false;
    }

    showMessage(message: string, success: boolean = true) {
        this.success = success;
        this.message = message;
    }
}
