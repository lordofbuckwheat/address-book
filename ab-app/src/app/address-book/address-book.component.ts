import { Component, OnInit } from '@angular/core';
import { Record } from '../record'
import {RecordService} from "../record.service";

@Component({
    selector: 'app-address-book',
    templateUrl: './address-book.component.html',
    styleUrls: ['./address-book.component.styl']
})
export class AddressBookComponent implements OnInit {

    constructor(
        private recordService: RecordService
    ) {}

    records: Record[];
    canCreate: boolean = true;

    ngOnInit() {
        this.records = this.recordService.getListFromPage();
    }

    delete(id: number) {
        this.records = this.records.filter((record: Record) => {
            return record.id !== id;
        })
    }

    add(record: Record) {
        this.records.unshift(record);//
    }

    search(query: string) {
        this.recordService.getList(query).then((records: Record[]) => {
            this.canCreate = query.length === 0;
            this.records = records;
        });
    }

}
