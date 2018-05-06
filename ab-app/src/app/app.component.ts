import {Component, ElementRef, OnInit} from '@angular/core';
import { Record } from './record'

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.styl']
})
export class AppComponent {
    title = 'Address Book';
}
