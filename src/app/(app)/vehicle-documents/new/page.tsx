import Link from "next/link";
import { buttonClasses } from "@/components/ui/Button";

export default function NewVehicleDocumentPage() {
  return (
    <div className="space-y-4">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">Yeni Araç/Şoför Ekle</h1>
        <p className="mt-1 text-sm opacity-70">
          Bu ekran form olarak tasarlanacak. Şimdilik placeholder.
        </p>
      </div>

      <div>
        <Link
          href="/vehicle-documents"
          className={buttonClasses({ variant: "secondary", size: "md" })}
        >
          Geri
        </Link>
      </div>
    </div>
  );
}
