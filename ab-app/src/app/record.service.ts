import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { Record } from './record'
import { catchError } from 'rxjs/operators';
import {ServerResponse} from "./server-response";
import {MessageService} from "./message.service";

@Injectable({
    providedIn: 'root'
})
export class RecordService {

    private baseUrl = '/';

    constructor(
        private http: HttpClient,
        private messageService: MessageService
    ) {}

    private handleRequest<T>(rq: Observable<ServerResponse>): Promise<T> {
        this.messageService.showLoading();
        return new Promise(resolve => {
            rq.pipe(
                catchError(this.handleHttpError({
                    success: true,
                    message: 'Request failed'
                }))
            ).subscribe((response: ServerResponse) => {
                this.messageService.showMessage(response.message, response.success);
                this.messageService.hideLoading();
                if (response.success) {
                    resolve(response.data);
                }
            });
        })
    }

    getListFromPage(): Record[] {
        let json = document.getElementsByTagName('body').item(0).getAttribute('data-records');
        return JSON.parse(json);
    }

    getList(search: string = ''): Promise<Record[]> {
        return this.handleRequest<Record[]>(this.http.get<ServerResponse>(`${this.baseUrl}record/read?search=${search}`));
    }

    update(record: Record): Promise<any> {
        return this.handleRequest(this.http.post<ServerResponse>(`${this.baseUrl}record/${record.id}/update`, record));
    }

    delete(record: Record): Promise<any> {
        return this.handleRequest(this.http.post<ServerResponse>(`${this.baseUrl}record/${record.id}/delete`, record));
    }

    create(record: Record): Promise<number> {
        return this.handleRequest(this.http.post<ServerResponse>(`${this.baseUrl}record/create`, record));
    }

    private handleHttpError (result: ServerResponse) {
        return (error: HttpErrorResponse): Observable<ServerResponse> => {
            console.error(error.message);
            return of(result);
        };
    }
}
