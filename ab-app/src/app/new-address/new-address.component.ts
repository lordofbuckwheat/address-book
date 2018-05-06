import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import {Record} from '../record';
import {RecordService} from "../record.service";

@Component({
    selector: 'app-new-address',
    templateUrl: './new-address.component.html',
    styleUrls: ['./new-address.component.styl']
})
export class NewAddressComponent implements OnInit {

    constructor(
        private recordService: RecordService
    ) { }

    @Output() onSubmit = new EventEmitter<Record>(false);

    record: Record = new Record();

    ngOnInit() {
    }

    submit() {
        this.recordService.create(this.record).then(id => {
            this.record.id = id;
            this.onSubmit.emit(this.record.clone());
            this.record.reset();
        });
    }

}
