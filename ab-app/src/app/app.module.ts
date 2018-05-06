import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';

import { AppComponent } from './app.component';
import { AddressBookComponent } from './address-book/address-book.component';
import { AddressComponent } from './address/address.component';
import { MessageBoxComponent } from './message-box/message-box.component';
import { NewAddressComponent } from './new-address/new-address.component';

@NgModule({
  declarations: [
    AppComponent,
    AddressBookComponent,
    AddressComponent,
    MessageBoxComponent,
    NewAddressComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpClientModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
