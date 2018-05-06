import {Component, OnInit, Input, Output, EventEmitter} from '@angular/core';
import {Record} from '../record';
import {RecordService} from "../record.service";

@Component({
    selector: 'app-address',
    templateUrl: './address.component.html',
    styleUrls: ['./address.component.styl']
})
export class AddressComponent implements OnInit {

    @Input() record: Record;
    recordEdited: Record;
    state: boolean = false;
    @Output() onDelete = new EventEmitter<number>();

    constructor(private recordService: RecordService) {}

    changeState() {
        this.state = !this.state;
    }

    edit() {
        this.recordService.update(this.recordEdited).then(() => {
            this.record = Object.assign({}, this.recordEdited);
            this.state = !this.state;
        })
    }

    delete() {
        this.recordService.delete(this.record).then(() => {
            this.onDelete.emit(this.record.id);
        });
    }

    ngOnInit() {
        this.recordEdited = Object.assign({}, this.record);
    }

}
