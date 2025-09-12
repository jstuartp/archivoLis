import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class EventService {
  private api = '/api';

  constructor(private http: HttpClient) {}

  getEvents() {
    return this.http.get<any[]>(`${this.api}/events`);
  }

  getEvent(name: string) {
    return this.http.get<any>(`${this.api}/events/${name}`);
  }

  downloadFiles(event: string, files: string[]) {
    return this.http.post(`${this.api}/events/${event}/download`, { files }, {
      responseType: 'blob'
    });
  }
}
