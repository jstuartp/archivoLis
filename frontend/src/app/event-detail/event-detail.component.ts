import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { EventService } from '../services/event.service';

interface FileEntry {
  file: string;
  station: string;
  pgaMax: number | null;
  selected?: boolean;
}

@Component({
  selector: 'app-event-detail',
  templateUrl: './event-detail.component.html'
})
export class EventDetailComponent implements OnInit {
  eventName!: string;
  info: any = {};
  files: FileEntry[] = [];
  sortField: 'station' | 'pgaMax' = 'station';

  constructor(private route: ActivatedRoute, private eventService: EventService) {}

  ngOnInit() {
    this.eventName = this.route.snapshot.params['name'];
    this.eventService.getEvent(this.eventName).subscribe(res => {
      this.info = res.info;
      this.files = res.files;
      this.sort();
    });
  }

  toggleSort(field: 'station' | 'pgaMax') {
    this.sortField = field;
    this.sort();
  }

  private sort() {
    this.files.sort((a, b) => {
      if (this.sortField === 'station') {
        return a.station.localeCompare(b.station);
      } else {
        return (b.pgaMax || 0) - (a.pgaMax || 0);
      }
    });
  }

  download() {
    const selected = this.files.filter(f => f.selected).map(f => f.file);
    if (!selected.length) return;
    this.eventService.downloadFiles(this.eventName, selected).subscribe(blob => {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'archivos.zip';
      a.click();
      window.URL.revokeObjectURL(url);
    });
  }
}
