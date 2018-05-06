import { Component, OnInit } from '@angular/core';
import {MessageService} from "../message.service";

@Component({
    selector: 'app-message-box',
    templateUrl: './message-box.component.html',
    styleUrls: ['./message-box.component.styl']
})
export class MessageBoxComponent implements OnInit {

    constructor(
        public service: MessageService
    ) { }

    ngOnInit() {
    }

}
